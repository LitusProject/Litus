<?php

namespace CudiBundle\Controller;

use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Entity\IsicCard;
use CudiBundle\Entity\Sale\Booking;
use Laminas\Soap\Client as SoapClient;
use Laminas\View\Model\ViewModel;
use SecretaryBundle\Component\Registration\Articles as RegistrationArticles;

class IsicController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function formAction()
    {
        $serviceUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.isic_service_url');

        $client = new SoapClient($serviceUrl);

        $academic = $this->checkAccess();
        if ($academic instanceof ViewModel) {
            return $academic;
        }

        $articleID = $this->isEnabled();
        if ($articleID instanceof ViewModel) {
            if ($this->getParam('redirect')) {
                $this->redirect()->toRoute(
                    $this->getParam('redirect'),
                    array(
                        'action' => $this->getParam('rediraction'),
                    )
                );

                return new ViewModel();
            }

            return $articleID;
        }

        $hasOrderedAlready = $this->hasPersonOrderedAlready($academic);
        if ($hasOrderedAlready) {
            if ($this->getParam('redirect')) {
                $this->redirect()->toRoute(
                    $this->getParam('redirect'),
                    array(
                        'action' => $this->getParam('rediraction'),
                    )
                );

                return new ViewModel();
            }

            return $hasOrderedAlready;
        }

        $isicMembership = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.isic_membership') == 1;

        if ($isicMembership) {
            if ($this->getParam('organization') == null || $this->getParam('size') == null) {
                $this->redirect()->toRoute(
                    'secretary_registration',
                    array()
                );

                return new ViewModel();
            }
        }

        $delayOrder = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.isic_delay_order') == 1;

        $form = $this->getForm('cudi_isic_order', $academic);

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            if ($form->isValid()) {
                $arguments = $form->hydrateObject();

                $config = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config');

                $arguments['username'] = $config->getConfigValue('cudi.isic_username');
                $arguments['password'] = $config->getConfigValue('cudi.isic_password');
                $arguments['ClientID'] = $config->getConfigValue('cudi.isic_client_id');
                $arguments['MemberNumber'] = '';
                $arguments['cardType'] = 'ISIC';
                $arguments['Nationality'] = '';
                $arguments['BirthPlace'] = '';
                $arguments['isStudent'] = '1';
                $arguments['sendToHome'] = '0';
                $arguments['promotionCode'] = '';
                $arguments['special'] = '0';
                if ($arguments['ISICCardNumber'] == '') {
                    $arguments['type'] = 'REQUESTED';
                } else {
                    $arguments['type'] = 'REVALIDATE';
                }

                $newsletterMandatory = $config->getConfigValue('cudi.isic_newsletter_mandatory');
                if ($newsletterMandatory == 1) {
                    $arguments['Optin'] = '1';
                }

                $newsletterMandatory = $config->getConfigValue('cudi.isic_partner_mandatory');
                if ($newsletterMandatory == 1) {
                    $arguments['partnerOptin'] = '1';
                }

                $result = '';
                $regex = '/^OK(S 032 (\d{3} ){3}[A-Za-z])$/i';
                if (getenv('APPLICATION_ENV') == 'development') {
                    $result = 'OKS 032 123 456 789 A';
                } else {
                    if ($delayOrder) {
                        $result = $client->addUnpaidIsicRegistration($arguments)->addUnpaidIsicRegistrationResult;
                        $regex = '/^OK(\d+)$/i';
                    } else {
                        $result = $client->addIsicRegistration($arguments)->addIsicRegistrationResult;
                    }
                }

                $capture = array();
                if (preg_match($regex, $result, $capture)) {
                    $article = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($articleID);

                    $booking = new Booking(
                        $this->getEntityManager(),
                        $academic,
                        $article,
                        $delayOrder ? 'assigned' : 'booked',
                        1,
                        true
                    );

                    $this->getEntityManager()->persist($booking);
                    $this->getEntityManager()->flush();

                    $isicCard = new IsicCard(
                        $academic,
                        $capture[1],
                        $booking,
                        !$delayOrder,
                        $this->getCurrentAcademicYear()
                    );

                    $this->getEntityManager()->persist($isicCard);
                    $this->getEntityManager()->flush();

                    if ($isicMembership) {
                        RegistrationArticles::book(
                            $this->getEntityManager(),
                            $academic,
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Organization')
                                ->findOneById($this->getParam('organization')),
                            $this->getCurrentAcademicYear(),
                            array(
                                'payed'      => false,
                                'tshirtSize' => $this->getParam('size'),
                            )
                        );
                    }

                    if ($this->getParam('redirect')) {
                        $this->redirect()->toRoute(
                            $this->getParam('redirect'),
                            array(
                                'action' => $this->getParam('rediraction'),
                            )
                        );

                        return new ViewModel();
                    }

                    return new ViewModel(
                        array(
                            'status' => 'success',
                            'info'   => $capture[1],
                        )
                    );
                } else {
                    return new ViewModel(
                        array(
                            'status' => 'error',
                            'error'  => $result,
                        )
                    );
                }
            }
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($articleID);

        $additionalConditions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.isic_Guido_conditions')
        );

        return new ViewModel(
            array(
                'status'               => 'form',
                'form'                 => $form,
                'price'                => $article->getSellPrice() / 100,
                'additionalConditions' => $additionalConditions[$this->getLanguage()->getAbbrev()],
            )
        );
    }

    private function checkAccess()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return new ViewModel(
                array(
                    'status' => 'noauth',
                )
            );
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            return new ViewModel(
                array(
                    'status' => 'noaccess',
                )
            );
        }

        return $academic;
    }

    private function hasPersonOrderedAlready($person)
    {
        if ($this->getEntityManager()            ->getRepository('CudiBundle\Entity\IsicCard')            ->findByPersonAndYearQuery($person, $this->getCurrentAcademicYear())            ->getResult()
        ) {
            return new ViewModel(
                array(
                    'status' => 'doubleorder',
                )
            );
        }
    }

    private function isEnabled()
    {
        $bookingsEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_bookings');

        if ($bookingsEnabled == 0) {
            return new ViewModel(
                array(
                    'status' => 'disabled',
                )
            );
        }

        $articleID = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.isic_sale_article');
        if ($articleID === '0') {
            return new ViewModel(
                array(
                    'status' => 'disabled',
                )
            );
        }

        return $articleID;
    }
}
