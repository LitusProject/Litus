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

namespace FormBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    FormBundle\Entity\Node\Group,
    FormBundle\Entity\Node\Group\Mapping,
    FormBundle\Entity\Node\Translation\Group as GroupTranslation,
    FormBundle\Form\Admin\Group\Add as AddForm,
    FormBundle\Form\Admin\Group\Edit as EditForm,
    FormBundle\Form\Admin\Group\Mapping as MappingForm,
    Zend\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Group')
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

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Group')
                ->findAllOld(),
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

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                $formData = $form->getFormData($formData);

                $group = new Group($this->getAuthentication()->getPersonObject());

                $this->getEntityManager()->persist($group);

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['introduction_' . $language->getAbbrev()]) {
                        $translation = new GroupTranslation(
                            $group,
                            $language,
                            $formData['title_' . $language->getAbbrev()],
                            $formData['introduction_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

                $startForm = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Form')
                    ->findOneById($formData['start_form']);

                $this->getEntityManager()->persist(new Mapping($startForm, $group, 1));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The group was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_admin_group',
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
        if (!($group = $this->_getGroup()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $group);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                $formData = $form->getFormData($formData);

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['introduction_' . $language->getAbbrev()]) {
                        $translation = $group->getTranslation($language, false);

                        if (null === $translation) {
                            $translation = new GroupTranslation(
                                $group,
                                $language,
                                $formData['title_' . $language->getAbbrev()],
                                $formData['introduction_' . $language->getAbbrev()]
                            );
                        } else {
                            $translation->setTitle($formData['title_' . $language->getAbbrev()])
                                ->setIntroduction($formData['introduction_' . $language->getAbbrev()]);
                        }

                        $this->getEntityManager()->persist($translation);
                    } else {
                        $translation = $group->getTranslation($language, false);

                        if ($translation !== null) {
                            $this->getEntityManager()->remove($translation);
                        }
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The group was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_admin_group',
                    array(
                        'action' => 'edit',
                        'id' => $group->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'group' => $group,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($group = $this->_getGroup()))
            return new ViewModel();

        $this->getEntityManager()->remove($group);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function formsAction()
    {
        if (!($group = $this->_getGroup()))
            return new ViewModel();

        $form = new MappingForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $form = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Form')
                    ->findOneById($formData['form']);

                if (sizeof($group->getForms()) > 0) {
                    $order = $group->getForms()[sizeof($group->getForms())-1]->getOrder() + 1;
                } else {
                    $order = 1;
                }

                $this->getEntityManager()->persist(new Mapping($form, $group, $order));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The form was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_admin_group',
                    array(
                        'action' => 'forms',
                        'id' => $group->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'group' => $group,
            )
        );
    }

    public function sortAction()
    {
        $this->initAjax();

        if(!($group = $this->_getGroup()))
            return new ViewModel();

        if(!$this->getRequest()->isPost())
            return new ViewModel();

        $data = $this->getRequest()->getPost();

        if(!$data['items'])
            return new ViewModel();

        foreach($data['items'] as $order => $id)
        {
            $mapping = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Group\Mapping')
                ->findOneById($id);
            $mapping->setOrder($order+1);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                )
            )
        );
    }

    public function deleteFormAction()
    {
        $this->initAjax();

        if (!($mapping = $this->_getMapping()))
            return new ViewModel();

        $this->getEntityManager()->remove($mapping);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _getGroup()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the group!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group')
            ->findOneById($this->getParam('id'));

        if (null === $group) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No group with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $group;
    }

    private function _getMapping()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the mapping!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneById($this->getParam('id'));

        if (null === $mapping) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No mapping with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $mapping;
    }
}