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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Controller\Admin;

use FormBundle\Component\Exception\UnsupportedTypeException,
    FormBundle\Entity\Field,
    FormBundle\Entity\Field\Checkbox as CheckboxField,
    FormBundle\Entity\Field\Dropdown as DropdownField,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Field\Text as StringField,
    FormBundle\Entity\Field\TimeSlot as TimeSlotField,
    FormBundle\Entity\Node\Form,
    Zend\View\Model\ViewModel;

/**
 * FieldController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FieldController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($formSpecification = $this->getFormEntity())) {
            return new ViewModel();
        }

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

        $fields = $formSpecification->getFields();

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'fields'            => $fields,
            )
        );
    }

    public function addAction()
    {
        if (!($formSpecification = $this->getFormEntity())) {
            return new ViewModel();
        }

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

        $latestField = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findLatestField($formSpecification);

        $form = $this->getForm(
            'form_field_add',
            array(
                'form'   => $formSpecification,
                'field'  => $this->getParam('repeat') ? $latestField : null,
                'repeat' => $this->getParam('repeat') == '1',
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                switch ($formData['type']) {
                    case 'string':
                        $field = new StringField($formSpecification);
                        break;
                    case 'dropdown':
                        $field = new DropdownField($formSpecification);
                        break;
                    case 'checkbox':
                        $field = new CheckboxField($formSpecification);
                        break;
                    case 'file':
                        $field = new FileField($formSpecification);
                        break;
                    case 'timeslot':
                        $field = new TimeSlotField($formSpecification);
                        break;
                    default:
                        throw new UnsupportedTypeException('This field type is unknown!');
                }

                $field = $form->hydrateObject($field);

                $formSpecification->addField($field);
                $this->getEntityManager()->persist($field);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The field was successfully created!'
                );

                if (null !== $this->getRequest()->getPost()->get('submit_repeat')) {
                    $this->redirect()->toRoute(
                        'form_admin_form_field',
                        array(
                            'action' => 'add',
                            'id'     => $formSpecification->getId(),
                            'repeat' => 1,
                        )
                    );
                } else {
                    $this->redirect()->toRoute(
                        'form_admin_form_field',
                        array(
                            'action' => 'manage',
                            'id'     => $formSpecification->getId(),
                        )
                    );
                }

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'form'              => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($field = $this->getFieldEntity())) {
            return new ViewModel();
        }

        if (!$field->getForm()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
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

        $form = $this->getForm(
            'form_field_edit',
            array(
                'form'   => $field->getForm(),
                'field'  => $field,
                'repeat' => false,
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The field was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'form_admin_form_field',
                    array(
                        'action' => 'manage',
                        'id'     => $field->getForm()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'formSpecification' => $field->getForm(),
                'form'              => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($field = $this->getFieldEntity())) {
            return new ViewModel();
        }

        if (!$field->getForm()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
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

        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByField($field);

        foreach ($entries as $entry) {
            $this->getEntityManager()->remove($entry);
        }

        $this->getEntityManager()->remove($field);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function sortAction()
    {
        $this->initAjax();

        if (!($formSpecification = $this->getFormEntity())) {
            return new ViewModel();
        }

        if (!$this->getRequest()->isPost()) {
            return new ViewModel();
        }

        $data = $this->getRequest()->getPost();

        if (!$data['items']) {
            return new ViewModel();
        }

        foreach ($data['items'] as $order => $id) {
            $field = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Field')
                ->findOneById($id);
            $field->setOrder($order + 1);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Form|null
     */
    private function getFormEntity()
    {
        $form = $this->getEntityById('FormBundle\Entity\Node\Form');

        if (!($form instanceof Form)) {
            $this->flashMessenger()->error(
                'Error',
                'No form was found!'
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

    /**
     * @return Field|null
     */
    private function getFieldEntity()
    {
        $field = $this->getEntityById('FormBundle\Entity\Field');

        if (!($field instanceof Field)) {
            $this->flashMessenger()->error(
                'Error',
                'No field was found!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $field;
    }
}
