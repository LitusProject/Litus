<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    MailBundle\Entity\Aliases\Academic as Alias,
    MailBundle\Form\Admin\Alias\Add as AddForm,
    Zend\View\Model\ViewModel;

class AliasController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Alias')
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
        $form = new AddForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if (!isset($formData['person_id']) || $formData['person_id'] == '') {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\People\Academic')
                        ->findOneByUsername($formData['person_name']);
                } else {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\People\Academic')
                        ->findOneById($formData['person_id']);
                }

                $alias = new Alias(
                    $formData['alias'], $academic
                );
                $this->getEntityManager()->persist($alias);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCES',
                        'The alias was succesfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_mail_alias',
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
        $this->initAjax();

        if (!($alias = $this->_getAlias()))
            return new ViewModel();

        $this->getEntityManager()->remove($alias);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getAlias()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the alias!'
                )
            );

            $this->redirect()->toRoute(
                'admin_mail_alias',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $alias = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Alias')
            ->findOneById($this->getParam('id'));

        if (null === $alias) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No alias with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_mail_alias',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $alias;
    }

}
