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

namespace PageBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    PageBundle\Entity\Link,
    PageBundle\Entity\Links\Translation,
    PageBundle\Form\Admin\Link\Add as AddForm,
    PageBundle\Form\Admin\Link\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * LinkController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class LinkController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'PageBundle\Entity\Link',
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
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $category = $this->getEntityManager()
                    ->getRepository('PageBundle\Entity\Category')
                    ->findOneById($formData['category']);

                $link = new Link($category, $formData['url']);

                if ('' != $formData['parent']) {
                    $parent = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Nodes\Page')
                        ->findOneById($formData['parent']);

                    $link->setParent($parent);
                }

                $this->getEntityManager()->persist($link);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if ('' != $formData['name_' . $language->getAbbrev()]) {
                        $translation = new Translation(
                            $link,
                            $language,
                            $formData['name_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The link was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_page_link',
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
        if (!($link = $this->_getLink()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $link);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $category = $this->getEntityManager()
                    ->getRepository('PageBundle\Entity\Category')
                    ->findOneById($formData['category']);

                $link->setCategory($category)
                    ->setUrl($formData['url']);

                if ('' != $formData['parent']) {
                    $parent = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Nodes\Page')
                        ->findOneById($formData['parent']);

                    $link->setParent($parent);
                }

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    $translation = $link->getTranslation($language, false);

                    if (null !== $translation) {
                        $translation->setName($formData['name_' . $language->getAbbrev()]);
                    } else {
                        if ('' != $formData['name_' . $language->getAbbrev()]) {
                            $translation = new Translation(
                                $link,
                                $language,
                                $formData['name_' . $language->getAbbrev()]
                            );

                            $this->getEntityManager()->persist($translation);
                        }
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The link was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_page_link',
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

        if (!($link = $this->_getLink()))
            return new ViewModel();

        $this->getEntityManager()->remove($link);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                )
            )
        );
    }

    private function _getLink()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the link!'
                )
            );

            $this->redirect()->toRoute(
                'admin_page_link',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $link = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Link')
            ->findOneById($this->getParam('id'));

        if (null === $link) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No link with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_page_link',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $link;
    }
}
