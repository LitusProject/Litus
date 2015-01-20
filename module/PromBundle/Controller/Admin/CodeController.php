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

use Zend\View\Model\ViewModel;

/**
 * CodeController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class CodeController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        return new ViewModel(array());
    }

    public function addAction()
    {
        $form = $this->getForm('prom_reservationCode_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                //TODO
                print_r($formData['nb_codes']);
                exit();

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The codes were successfully generated!'
                );

                $this->redirect()->toRoute(
                    'promo_admin_code',
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

    public function deleteAction()
    {
        return new ViewModel();
    }
}
