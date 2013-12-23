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

namespace FormBundle\Controller\Manage;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    CommonBundle\Component\Document\Generator\Csv as CsvGenerator,
    FormBundle\Entity\Entry as FieldEntry,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Form\Manage\Mail\Send as MailForm,
    FormBundle\Form\SpecifiedForm\Doodle as DoodleForm,
    FormBundle\Form\SpecifiedForm\Edit as SpecifiedForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * FormController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FormController extends \FormBundle\Component\Controller\FormController
{
    public function indexAction()
    {
        if (!($person = $this->getAuthentication()->getPersonObject()))
            return new ViewModel();

        $viewerMaps = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findAllByPerson($person);

        $forms = array();
        foreach ($viewerMaps as $viewerMap)
            $forms[] = $viewerMap->getForm();

        return new ViewModel(
            array(
                'forms' => $forms,
            )
        );
    }

    public function viewAction()
    {
        if (!($person = $this->getAuthentication()->getPersonObject()))
            return new ViewModel();

        if(!($form = $this->_getForm()))
            return new ViewModel();

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $form);

        if (!$viewerMap) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to the given form!'
                )
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'index'
                )
            );

            return new ViewModel();
        }

        // Refetch fields to make sure they are ordered
        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($form);

        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($form);

        $mailForm = new MailForm();
        $mailForm->setAttribute('action', $this->url()->fromRoute('form_manage_mail', array('action' => 'send', 'id' => $form->getId())));

        return new ViewModel(
            array(
                'form'     => $form,
                'fields'   => $fields,
                'entries'  => $entries,
                'viewer'   => $viewerMap,
                'mailForm' => $mailForm,
            )
        );
    }

    public function editAction()
    {
        if (!($person = $this->getAuthentication()->getPersonObject()))
            return new ViewModel();

        if (!($formEntry = $this->_getEntry()))
            return new ViewModel();

        $formSpecification = $formEntry->getForm();

        if ($formSpecification->getType() == 'doodle') {
            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action'   => 'doodle',
                    'id'       => $formEntry->getId(),
                )
            );

            return new ViewModel();
        }

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $formEntry->getForm());

        if (!$viewerMap || !$viewerMap->isEdit()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to edit the given form!'
                )
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'view',
                    'id'     => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $form = new SpecifiedForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $formEntry, $formEntry->getCreationPerson());
        $form->populateFromEntry($formEntry);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                foreach ($formSpecification->getFields() as $field) {
                    $value = $formData['field-' . $field->getId()];

                    $fieldEntry = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Entry')
                        ->findOneByFormEntryAndField($formEntry, $field);

                    if ($field instanceof FileField) {
                        $filePath = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('form.file_upload_path');

                        $upload = new FileUpload();
                        $upload->setValidators($form->getInputFilter()->get('field-' . $field->getId())->getValidatorChain()->getValidators());
                        if ($upload->isValid('field-' . $field->getId())) {
                            if ($fieldEntry->getValue() == '') {
                                $fileName = '';
                                do{
                                    $fileName = sha1(uniqid());
                                } while (file_exists($filePath . '/' . $fileName));
                            } else {
                                $fileName = $fieldEntry->getValue();
                            }

                            $upload->addFilter('Rename', $filePath . '/' . $fileName, 'field-' . $field->getId());
                            $upload->receive('field-' . $field->getId());

                            $value = $fileName;
                        } elseif (!(sizeof($upload->getMessages()) == 1 && isset($upload->getMessages()['fileUploadErrorNoFile']))) {
                            $form->setMessages(array('field-' . $field->getId() => $upload->getMessages()));

                            return new ViewModel(
                                array(
                                    'specification' => $entry->getForm(),
                                    'form'          => $form,
                                )
                            );
                        } else {
                            $value = $fieldEntry->getValue();
                        }
                    }

                    if ($fieldEntry) {
                        $fieldEntry->setValue($value);
                    } else {
                        $fieldEntry = new FieldEntry($formEntry, $field, $value);
                        $formEntry->addFieldEntry($fieldEntry);
                        $this->getEntityManager()->persist($fieldEntry);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The entry was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_manage',
                    array(
                        'action' => 'view',
                        'id'     => $formSpecification->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'formSpecification' => $formSpecification,
                'entry' => $formEntry,
            )
        );
    }

    public function doodleAction()
    {
        if (!($person = $this->getAuthentication()->getPersonObject()))
            return new ViewModel();

        if (!($formEntry = $this->_getEntry()))
            return new ViewModel();

        $formSpecification = $formEntry->getForm();
        $formSpecification->setEntityManager($this->getEntityManager());

        if ($formSpecification->getType() != 'doodle') {
            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action'   => 'edit',
                    'id'       => $formEntry->getId(),
                )
            );

            return new ViewModel();
        }

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $formEntry->getForm());

        if (!$viewerMap || !$viewerMap->isEdit()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to edit the given form!'
                )
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'view',
                    'id'     => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $formEntries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($formSpecification);

        $occupiedSlots = array();
        foreach($formEntries as $entry) {
            if ($entry->getCreationPerson() == $formEntry->getCreationPerson())
                continue;

            foreach($entry->getFieldEntries() as $fieldEntry) {
                $occupiedSlots[$fieldEntry->getField()->getId()] = $entry->getPersonInfo()->getFullName();
            }
        }

        $notValid = false;
        $form = new DoodleForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $formEntry->getCreationPerson(), $formEntry, $occupiedSlots, true);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                foreach($formEntry->getFieldEntries() as $fieldEntry) {
                    $this->getEntityManager()->remove($fieldEntry);
                }
                $this->getEntityManager()->flush();

                foreach ($formSpecification->getFields() as $field) {
                    if (isset($formData['field-' . $field->getId()]) && $formData['field-' . $field->getId()]) {
                        $fieldEntry = new FieldEntry($formEntry, $field, '1');
                        $formEntry->addFieldEntry($fieldEntry);
                        $this->getEntityManager()->persist($fieldEntry);

                        if (!$formSpecification->isMultiple())
                            break;
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The entry was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_manage',
                    array(
                        'action'   => 'doodle',
                        'id'       => $formEntry->getId(),
                    )
                );

                return new ViewModel();
            } else {
                $notValid = true;
            }
        }

        return new ViewModel(
            array(
                'formEntry'         => $formEntry,
                'formSpecification' => $formSpecification,
                'form'              => $form,
                'occupiedSlots'     => $occupiedSlots,
                'doodleNotValid'    => $notValid,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($person = $this->getAuthentication()->getPersonObject()))
            return new ViewModel();

        if (!($formEntry = $this->_getEntry()))
            return new ViewModel();

        $formSpecification = $formEntry->getForm();

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $formEntry->getForm());

        if (!$viewerMap || !$viewerMap->isEdit()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to edit the given form!'
                )
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'view',
                    'id'     => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $this->getEntityManager()->remove($formEntry);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function downloadAction()
    {
        if (!($person = $this->getAuthentication()->getPersonObject()))
            return new ViewModel();

        if(!($form = $this->_getForm()))
            return new ViewModel();

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $form);

        if (!$viewerMap) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to the given form!'
                )
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'index'
                )
            );

            return new ViewModel();
        }

        $file = new CsvFile();

        $language = $this->getLanguage();
        $heading = array('ID', 'Submitter', 'Submitted');
        if ($viewerMap->isMail()) {
            $heading[] = 'Email';
        }

        if ($form->getType() == 'doodle') {
            $entries = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findAllByForm($form);

            $maxSlots = 0;
            foreach ($entries as $entry) {
                $result = array($entry->getId(), $entry->getPersonInfo()->getFullName(), $entry->getCreationTime()->format('d/m/Y H:i'));
                if ($viewerMap->isMail())
                    $result[] = $entry->getPersonInfo()->getEmail();

                $maxSlots = max(sizeof($entry->getFieldEntries()), $maxSlots);
                foreach($entry->getFieldEntries() as $fieldEntry) {
                    $result[] = $fieldEntry->getField()->getStartDate()->format('d/m/Y H:i');
                    $result[] = $fieldEntry->getField()->getEndDate()->format('d/m/Y H:i');
                }
                $results[] = $result;
            }

            for ($i = 0 ; $i < $maxSlots ; $i++) {
                $heading[] = 'Slot ' . ($i+1) . ' Start';
                $heading[] = 'Slot ' . ($i+1) . ' End';
            }
        } else {
            $fields = $form->getFields();
            foreach ($fields as $field) {
                $heading[] = $field->getLabel($language);
            }

            $entries = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findAllByForm($form);

            $results = array();
            foreach ($entries as $entry) {
                $result = array($entry->getId(), $entry->getPersonInfo()->getFirstName() . ' ' . $entry->getPersonInfo()->getLastName(), $entry->getCreationTime()->format('d/m/Y H:i'));
                if ($viewerMap->isMail())
                    $result[] = $entry->getPersonInfo()->getEmail();

                foreach($fields as $field) {
                    $fieldEntry = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Entry')
                        ->findOneByFormEntryAndField($entry, $field);
                    if ($fieldEntry)
                        $result[] = $fieldEntry->getValueString($language);
                    else
                        $result[] = '';
                }
                $results[] = $result;
            }
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="results.csv"',
            'Content-Type'        => 'text/csv',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function downloadFileAction()
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('form.file_upload_path') . '/' . $this->getParam('id');

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $this->getParam('id') . '"',
            'Content-Type' => mime_content_type($filePath),
            'Content-Length' => filesize($filePath),
        ));
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath, 'r');
        $data = fread($handle, filesize($filePath));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    private function _getForm()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the form!'
                )
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        $formSpecification = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findOneById($this->getParam('id'));

        if (null === $formSpecification) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No form with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        $formSpecification->setEntityManager($this->getEntityManager());

        return $formSpecification;
    }

    private function _getEntry()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the entry!'
                )
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        $entry = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findOneById($this->getParam('id'));

        if (null === $entry) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No entry with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        return $entry;
    }

}
