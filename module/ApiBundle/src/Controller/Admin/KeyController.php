<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ApiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    ApiBundle\Entity\Key,
    ApiBundle\Form\Admin\Key\Add as AddForm,
    ApiBundle\Form\Admin\Key\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * KeyController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class KeyController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('ApiBundle\Entity\Key')
                ->findAllActive(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                do {
                    $code = md5(uniqid(rand(), true));
                    $found = $this->getEntityManager()
                        ->getRepository('ApiBundle\Entity\Key')
                        ->findOneByCode($code);
                } while(isset($found));

                $key = new Key(
                    $formData['host'],
                    $code
                );
                $this->getEntityManager()->persist($key);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The key was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_key',
                    array(
                        'action' => 'manage'
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

    public function editAction()
    {
        if (!($key = $this->_getKey()))
            return new ViewModel();

        $form = new EditForm($key);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                $key->setHost($formData['host']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The key was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_key',
                    array(
                        'action' => 'manage'
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

        if (!($key = $this->_getKey()))
            return new ViewModel();

        $key->revoke();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _getKey()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the key!'
                )
            );

            $this->redirect()->toRoute(
                'admin_key',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $key = $this->getEntityManager()
            ->getRepository('ApiBundle\Entity\Key')
            ->findOneById($this->getParam('id'));

        if (null === $key) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No key with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_key',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $key;
    }
}
