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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller;

use Zend\View\Model\ViewModel,
    CudiBundle\Entity\Sale\Booking,
    Zend\Soap\Client as SoapClient;

class IsicController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    protected $client;

    public function __construct()
    {
        $this->client = new SoapClient('http://isicregistrations.guido.be/service.asmx?WSDL');
    }
    
    public function formAction()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return new ViewModel();
        }

        $academic =$this->getAuthentication()->getPersonObject();

        /*
        $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById(8145);
        */

        if (!($academic instanceof Academic)) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_isic_order', $academic);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'cudi_isic',
                array(
                    'action' => 'order',
                )
            )
        );

        $saleArticle = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Article')
                            ->findOneById($this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('cudi.isic_sale_article'));

        return new ViewModel(
            array(
                'form' => $form,
                'price' => $saleArticle->getSellPrice() / 100 . '€',
            )
        );
    }

    public function orderAction()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return new ViewModel();
        }

        $academic = $this->getAuthentication()->getPersonObject();

        /*
        $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById(8145);
        */

        if (!($academic instanceof Academic)) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_isic_order');

        if ($this->getRequest()->isPost()) {
            $form->setData(array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            ));

            if ($form->isValid()) {
                $formData = $form->getData();

                $config = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config');

                $arguments = $form->hydrateObject();

                $arguments['Username'] = $config->getConfigValue('cudi.isic_username');
                $arguments['Password'] = $config->getConfigValue('cudi.isic_password');
                $arguments['MemberNumber'] = '';
                $arguments['cardType'] = 'ISIC';
                $arguments['Nationality'] = '';
                $arguments['isStudent'] = '1';
                $arguments['sendToHome'] = '0';
                $arguments['ClientID'] = '';
                $arguments['promotionCode'] = '';
                $arguments['special'] = '0';
                if ($arguments['ISICCardNumber'] == '') {
                    $arguments['Type'] = 'REQUESTED';
                } else {
                    $arguments['Type'] = 'REVALIDATE';
                }

                $result = $this->client->addIsicRegistration($arguments);

                $arguments['Photo'] = '';

                $article = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Article')
                            ->findOneById($config->getConfigValue('cudi.isic_sale_article'));

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

                return new ViewModel(
                    array(
                        'status' => 'success',
                        'info' => array(
                            'result' => $result,
                            'arguments' => $arguments,
                            'file' => $formData['photo_group']['photo'],
                        ),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form' => array(
                            'errors' => $form->getMessages(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'status' => 'error',
                'form' => array(
                    'errors' => array(),
                ),
            )
        );
    }
}
