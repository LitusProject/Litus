<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller;

use CudiBundle\Entity\IsicCard,
    CudiBundle\Entity\Sale\Booking,
    Zend\Soap\Client as SoapClient,
    Zend\View\Model\ViewModel;

class IsicController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    protected $client;

    public function __construct()
    {
        $this->client = new SoapClient('http://isicregistrations.guido.be/service.asmx?WSDL');
    }

    private function isMember($academic)
    {
        $academicYear = $this->getCurrentAcademicYear();

        return $academic->isMember($academicYear) || $academic->isPraesidium($academicYear);
    }

    private function isEnabled()
    {
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

    private function checkAccess()
    {
        if ('development' == getenv('APPLICATION_ENV')) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById(8145);
        }

        if (!$this->getAuthentication()->isAuthenticated()) {
            return new ViewModel(
                array(
                    'status' => 'noauth',
                )
            );
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!$this->isMember($academic)) {
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
        if ($this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\IsicCard')
                        ->findByPersonQuery($person)
                        ->getResult()) {
            return new ViewModel(
                array(
                    'status' => 'doubleorder',
                )
            );
        }
    }

    public function formAction()
    {
        $academic = $this->checkAccess();
        if ($academic instanceof ViewModel) {
            return $academic;
        }

        $articleID = $this->isEnabled();
        if ($articleID instanceof ViewModel) {
            return $articleID;
        }

        $hasOrderedAlready = $this->hasPersonOrderedAlready($academic);
        if ($hasOrderedAlready) {
            return $hasOrderedAlready;
        }

        $form = $this->getForm('cudi_isic_order', $academic);

        if ($this->getRequest()->isPost()) {
            $form->setData(array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            ));

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
                $arguments['isStudent'] = '1';
                $arguments['sendToHome'] = '0';
                $arguments['promotionCode'] = '';
                $arguments['special'] = '0';
                if ($arguments['ISICCardNumber'] == '') {
                    $arguments['type'] = 'REQUESTED';
                } else {
                    $arguments['type'] = 'REVALIDATE';
                }
                $result = $this->client->addIsicRegistration($arguments);
                if ('development' == getenv('APPLICATION_ENV') && $result->addIsicRegistrationResult === 'CARDNUMBERS ARE DEPLETED.') {
                    $result->addIsicRegistrationResult = 'OKS 032 123 456 789 A';
                }
                $capture = array();

                if (preg_match('/^OK(S 032 (\d{3} ){3}[A-Za-z])$/i', $result->addIsicRegistrationResult, $capture)) {
                    $article = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Article')
                            ->findOneById($articleID);

                    $booking = new Booking(
                        $this->getEntityManager(),
                        $academic,
                        $article,
                        'booked',
                        1,
                        true
                    );

                    $this->getEntityManager()->persist($booking);
                    $this->getEntityManager()->flush();

                    $isicCard = new IsicCard(
                        $academic,
                        $capture[1],
                        $booking,
                        $this->getCurrentAcademicYear()
                    );

                    $this->getEntityManager()->persist($isicCard);
                    $this->getEntityManager()->flush();

                    return new ViewModel(
                        array(
                            'status' => 'success',
                            'info' => array(
                                'result' => $result,
                                'cardID' => $capture[1],
                            ),
                        )
                    );
                } else {
                    return new ViewModel(
                        array(
                            'status' => 'error',
                            'error' => $result->addIsicRegistrationResult,
                        )
                    );
                }
            }
        }

        $article = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Article')
                            ->findOneById($articleID);

        return new ViewModel(
            array(
                'status' => 'form',
                'form' => $form,
                'price' => $article->getSellPrice() / 100,
            )
        );
    }
}
