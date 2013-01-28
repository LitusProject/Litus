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
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getDocumentManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ('' == $formData['name']) {
                    do {
                        $name = $this->_createRandomName();
                        $found = $this->getDocumentManager()
                            ->getRepository('OnBundle\Document\Slug')
                            ->findOneByName($name);
                    } while(isset($found));
                }

                $slug = new Slug(
                    $this->getAuthentication()->getPersonObject(),
                    ('' == $formData['name'] ? $name : $formData['name']),
                    $formData['url']
                );
                $this->getDocumentManager()->persist($slug);

                $this->getDocumentManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The slug was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_slug',
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
        if (!($slug = $this->_getSlug()))
            return new ViewModel();

        $form = new EditForm($this->getDocumentManager(), $slug);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $slug->setName($formData['name'])
                    ->setUrl($formData['url']);

                $this->getDocumentManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The key was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_slug',
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

        if (!($slug = $this->_getSlug()))
            return new ViewModel();

        $this->getDocumentManager()->remove($slug);

        $this->getDocumentManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _createRandomName()
    {
        $characters = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';

        $name = array();
        for ($i = 0; $i < 8; $i++)
            $name[$i] = $characters[rand(0, strlen($characters) - 1)];

        return implode($name);
    }

    private function _getSlug()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the slug!'
                )
            );

            $this->redirect()->toRoute(
                'admin_slug',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $slug = $this->getDocumentManager()
            ->getRepository('OnBundle\Document\Slug')
            ->findOneById($this->getParam('id'));

        if (null === $slug) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No slug with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_slug',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $slug;
    }
}
