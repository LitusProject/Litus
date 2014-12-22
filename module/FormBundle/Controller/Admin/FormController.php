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

namespace FormBundle\Controller\Admin;




use FormBundle\Entity\Node\Form\Doodle,
    FormBundle\Entity\Node\Form\Form,
    FormBundle\Entity\ViewerMap,
    Zend\View\Model\ViewModel;

/**
 * FormController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FormController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Form')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        foreach ($paginator as $form) {
            $form->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Form')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        foreach ($paginator as $form) {
            $form->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'entityManager' => $this->getEntityManager(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('form_form_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                if ($formData['type'] == 'doodle') {
                    $formEntity = new Doodle($this->getAuthentication()->getPersonObject());
                } else {
                    $formEntity = new Form($this->getAuthentication()->getPersonObject());
                }

                $formEntity = $form->hydrateObject($formEntity);

                $this->getEntityManager()->persist($formEntity);

                $map = new ViewerMap($formEntity, $this->getAuthentication()->getPersonObject(), true, true);

                $this->getEntityManager()->persist($map);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The form was successfully added!'
                );

                $this->redirect()->toRoute(
                    'form_admin_form',
                    array(
                        'action' => 'edit',
                        'id' => $formEntity->getId(),
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
        if (!($formSpecification = $this->_getForm())) {
            return new ViewModel();
        }

        $formSpecification->setEntityManager($this->getEntityManager());

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($formSpecification);

        if (!$formSpecification->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to edit this form!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $form = $this->getForm('form_form_edit', array('form' => $formSpecification));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The form was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'form_admin_form',
                    array(
                        'action' => 'edit',
                        'id' => $formSpecification->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'group' => $group,
                'form' => $form,
                'formSpecification' => $formSpecification,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($form = $this->_getForm())) {
            return new ViewModel();
        }

        if (!$form->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to delete this form!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($form);

        foreach ($fields as $field) {
            $this->_deleteField($field);
        }

        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($form);

        foreach ($entries as $entry) {
            $this->getEntityManager()->remove($entry);
        }

        $viewers = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findAllByForm($form);

        foreach ($viewers as $viewer) {
            $this->getEntityManager()->remove($viewer);
        }

        $this->getEntityManager()->remove($form);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    private function _deleteField($field)
    {
        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByField($field);

        foreach ($entries as $entry) {
            $this->getEntityManager()->remove($entry);
        }

        $this->getEntityManager()->remove($field);
    }

    private function _getForm()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the form!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $form = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findOneById($this->getParam('id'));

        if (null === $form) {
            $this->flashMessenger()->error(
                'Error',
                'No form with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $form;
    }
}
