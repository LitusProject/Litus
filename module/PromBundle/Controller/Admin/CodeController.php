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

namespace PromBundle\Controller\Admin;

use PromBundle\Entity\Bus\ReservationCode,
    Zend\View\Model\ViewModel;

/**
 * CodeController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class CodeController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                ->findAll(),
            $this->getParam('page')
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
        $form = $this->getForm('prom_reservationCode_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                for ($i = 0; $i < $formData['nb_codes']; $i++) {
                    $newCode = new ReservationCode();
                    $this->getEntityManager()->persist($newCode);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The codes were successfully generated!'
                );

                $this->redirect()->toRoute(
                    'prom_admin_code',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function expireAction()
    {
        return new ViewModel();
    }

    public function viewAction()
    {
        if (!($code = $this->_getCode())) {
            return new ViewModel();
        }

        if ($code->isUsed()) {
            $passenger = $this->getEntityManager()
                ->getRepository('PromBundle\Entity\Bus\Passenger')
                ->findPassengerByCode($code);
        } else {
            $passenger = null;
        }

        return new ViewModel(
            array(
                'passenger' => $passenger[0],
                'code' => $code,
            )
        );
    }

    private function _getCode()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the code!'
            );

            $this->redirect()->toRoute(
                'prom_admin_code',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $code = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->findOneById($this->getParam('id'));

        if (null === $code) {
            $this->flashMessenger()->error(
                'Error',
                'No code with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'prom_admin_bus',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $code;
    }
}
