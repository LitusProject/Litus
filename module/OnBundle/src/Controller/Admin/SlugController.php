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

namespace OnBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    OnBundle\Document\Slug,
    OnBundle\Form\Admin\Slug\Add as AddForm,
    OnBundle\Form\Admin\Slug\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * SlugController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SlugController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromDocument(
            'OnBundle\Document\Slug',
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
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
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
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
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
