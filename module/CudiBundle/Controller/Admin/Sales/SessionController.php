<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin\Sales;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Bank\BankDevice\Amount as BankDeviceAmount,
    CommonBundle\Entity\General\Bank\CashRegister,
    CommonBundle\Entity\General\Bank\MoneyUnit\Amount as MoneyUnitAmount,
    CudiBundle\Entity\Sales\Session,
    CudiBundle\Form\Admin\Sales\Session\Add as AddForm,
    CudiBundle\Form\Admin\Sales\Session\Edit as EditForm,
    CudiBundle\Form\Admin\Sales\Session\Close as CloseForm,
    CudiBundle\Form\Admin\Sales\Session\Comment as CommentForm,
    Zend\View\Model\ViewModel;

/**
 * SessionController
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SessionController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Sales\Session',
            $this->getParam('page'),
            array(),
            array('openDate' => 'DESC')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        $lastSession = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->getLast();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $cashRegister = new CashRegister();
                $this->getEntityManager()->persist($cashRegister);

                $devices = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                    ->findAll();
                foreach($devices as $device) {
                    $amountDevice = new BankDeviceAmount($cashRegister, $device, $formData['device_'.$device->getId()]);
                    $this->getEntityManager()->persist($amountDevice);
                }

                $units = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                    ->findAll();
                foreach($units as $unit) {
                    $amountUnit = new MoneyUnitAmount($cashRegister, $unit, $formData['unit_'.$unit->getId()]);
                    $this->getEntityManager()->persist($amountUnit);
                }

                $session = new Session($cashRegister, $this->getAuthentication()->getPersonObject());
                $this->getEntityManager()->persist($session);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The session was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session',
                    array(
                        'action' => 'edit',
                        'id' => $session->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'lastSession' => $lastSession,
            )
        );
    }

    public function editAction()
    {
        if (!($session = $this->_getSession()))
            return new ViewModel();

        $form = new CommentForm($session);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $session->setComment($formData['comment']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The comment was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session',
                    array(
                        'action' => 'edit',
                        'id' => $session->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
            ->findAll();

        $devices = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
            ->findAll();

        return new ViewModel(
            array(
                'session' => $session,
                'units'   => $units,
                'devices' => $devices,
                'form' => $form,
            )
        );
    }

    public function editRegisterAction()
    {
        if (!($cashRegister = $this->_getCashRegister()))
            return new ViewModel();

        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneByCashRegister($cashRegister);

        $form = new EditForm($this->getEntityManager(), $cashRegister);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $devices = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                    ->findAll();

                foreach($devices as $device) {
                    $cashRegister->getAmountForDevice($device)
                        ->setAmount($formData['device_'.$device->getId()]);
                }

                $units = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                    ->findAll();

                foreach($units as $unit) {
                    $cashRegister->getAmountForUnit($unit)
                        ->setAmount($formData['unit_'.$unit->getId()]);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The cash register was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session',
                    array(
                        'action' => 'edit',
                        'id' => $session->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'session' => $session,
            )
        );
    }

    public function closeAction()
    {
        if (!($session = $this->_getSession()))
            return new ViewModel();

        $form = new CloseForm($this->getEntityManager(), $session->getOpenRegister());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $cashRegister = new CashRegister();

                $devices = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                    ->findAll();
                foreach($devices as $device) {
                    $amountDevice = new BankDeviceAmount($cashRegister, $device, $formData['device_'.$device->getId()]);
                    $this->getEntityManager()->persist($amountDevice);
                }

                $units = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                    ->findAll();
                foreach($units as $unit) {
                    $amountUnit = new MoneyUnitAmount($cashRegister, $unit, $formData['unit_'.$unit->getId()]);
                    $this->getEntityManager()->persist($amountUnit);
                }

                $autoExpire = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.enable_automatic_expire');

                if ('1' == $autoExpire) {
                    $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sales\Booking')
                        ->expireBookings($this->getMailTransport());
                }

                $session->close($cashRegister);

                $this->getEntityManager()->persist($cashRegister);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The session was successfully closed!'
                    )
                );

                   $this->redirect()->toRoute(
                       'cudi_admin_sales_session',
                       array(
                           'action' => 'edit',
                           'id' => $session->getId(),
                       )
                   );

                   return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'session' => $session,
            )
        );
    }

    public function queueItemsAction()
    {
        if (!($session = $this->_getSession()))
            return new ViewModel();

        $items = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findBySession($session);

        return new ViewModel(
            array(
                'session' => $session,
                'items' => $items,
            )
        );
    }

    public function killSocketAction()
    {
        $this->initAjax();

        system("kill $(ps aux | grep -i \"php bin/CudiBundle/queue.php --run\" | grep -v grep | awk '{print $2}')");

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getSession()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the session!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneById($this->getParam('id'));

        if (null === $session) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No session with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $session;
    }

    private function _getCashRegister()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the cash register!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $cashRegister = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\CashRegister')
            ->findOneById($this->getParam('id'));

        if (null === $cashRegister) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No cash register with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $cashRegister;
    }
}
