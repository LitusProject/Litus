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

namespace FormBundle\Controller\Manage;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    FormBundle\Component\Document\Generator\Doodle as DoodleGenerator,
    FormBundle\Component\Document\Generator\Form as FormGenerator,
    FormBundle\Component\Document\Generator\Zip as ZipGenerator,
    FormBundle\Entity\Field,
    FormBundle\Entity\Node\Entry as FormEntry,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\GuestInfo,
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
        if (!($person = $this->getPersonEntity())) {
            return new ViewModel();
        }

        $viewerMaps = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findAllByPerson($person);

        $forms = array();
        foreach ($viewerMaps as $viewerMap) {
            $forms[] = $viewerMap->getForm();
        }

        return new ViewModel(
            array(
                'forms' => $forms,
            )
        );
    }

    public function viewAction()
    {
        if (!($person = $this->getPersonEntity())) {
            return new ViewModel();
        }

        if (!($form = $this->getFormEntity())) {
            return new ViewModel();
        }

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $form);

        if (!$viewerMap) {
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to the given form!'
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'index',
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

        $mailForm = $this->getForm('form_manage_mail_send');
        $mailForm->setAttribute(
            'action',
            $this->url()->fromRoute(
                'form_manage_mail',
                array(
                    'action' => 'send',
                    'id' => $form->getId(),
                )
            )
        );

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

    public function addAction()
    {
        if (!($person = $this->getPersonEntity())) {
            return new ViewModel();
        }

        if (!($formSpecification = $this->getFormEntity())) {
            return new ViewModel();
        }

        if ($formSpecification->getType() == 'doodle') {
            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action'   => 'doodleAdd',
                    'id'       => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $formSpecification);

        if (!$viewerMap || !$viewerMap->isEdit()) {
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to edit the given form!'
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

        $form = $this->getForm(
            'form_manage_specified-form_add',
            array(
                'form' => $formSpecification,
                'language' => $this->getLanguage(),
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData(array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            ));

            if ($form->isValid()) {
                $formData = $form->getData();

                $person = null;
                if (isset($formData['person_form'])) {
                    $person = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person')
                        ->findOneById($formData['person_form']['person']['id']);
                }

                $formEntry = new FormEntry($formSpecification, $person);
                if (null === $person) {
                    $formEntry->setGuestInfo(
                        new GuestInfo($this->getEntityManager(), $this->getRequest())
                    );
                }

                $formEntry = $form->hydrateObject($formEntry);

                $this->getEntityManager()->persist($formEntry);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The entry was successfully added.'
                );

                $this->redirect()->toRoute(
                    'form_manage',
                    array(
                        'action' => 'view',
                        'id'     => $formSpecification->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'formSpecification' => $formSpecification,
            )
        );
    }

    public function editAction()
    {
        if (!($person = $this->getPersonEntity())) {
            return new ViewModel();
        }

        if (!($formEntry = $this->getEntryEntity())) {
            return new ViewModel();
        }

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
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to edit the given form!'
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

        $form = $this->getForm(
            'form_specified-form_edit',
            array(
                'form' => $formSpecification,
                'person' => $formEntry->getCreationPerson(),
                'language' => $this->getLanguage(),
                'entry' => $formEntry,
                'guest_info' => $formEntry->getGuestInfo(),
                'is_draft' => false,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData(array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            ));

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The entry was successfully edited!'
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

    public function doodleAddAction()
    {
        if (!($formSpecification = $this->getFormEntity())) {
            return new ViewModel();
        }

        if ($formSpecification->getType() == 'form') {
            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action'   => 'add',
                    'id'       => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $form = $this->getForm(
            'form_manage_specified-form_doodle',
            array(
                'form' => $formSpecification,
                'language' => $this->getLanguage(),
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $person = null;
                if (isset($formData['person_form'])) {
                    $person = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person')
                        ->findOneById($formData['person_form']['person']['id']);
                }

                $formEntry = new FormEntry($formSpecification, $person);
                if (null === $person) {
                    $formEntry->setGuestInfo(
                        new GuestInfo($this->getEntityManager(), $this->getRequest())
                    );
                }

                $formEntry = $form->hydrateObject($formEntry);

                $this->getEntityManager()->persist($formEntry);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The entry was successfully added.'
                );

                $this->redirect()->toRoute(
                    'form_manage',
                    array(
                        'action'   => 'view',
                        'id'       => $formSpecification->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'formSpecification' => $formSpecification,
            )
        );
    }

    public function doodleAction()
    {
        if (!($person = $this->getPersonEntity())) {
            return new ViewModel();
        }

        if (!($formEntry = $this->getEntryEntity())) {
            return new ViewModel();
        }

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
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to edit the given form!'
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

        $notValid = false;
        $form = $this->getForm(
            'form_specified-form_doodle',
            array(
                'form' => $formSpecification,
                'person' => $formEntry->getCreationPerson(),
                'guestInfo' => $formEntry->getGuestInfo(),
                'language' => $this->getLanguage(),
                'entry' => $formEntry,
                'forceEdit' => true,
            )
        );
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The entry was successfully edited!'
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
                'doodleNotValid'    => $notValid,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($person = $this->getPersonEntity())) {
            return new ViewModel();
        }

        if (!($formEntry = $this->getEntryEntity())) {
            return new ViewModel();
        }

        $formSpecification = $formEntry->getForm();

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $formEntry->getForm());

        if (!$viewerMap || !$viewerMap->isEdit()) {
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to edit the given form!'
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
        if (!($person = $this->getPersonEntity())) {
            return new ViewModel();
        }

        if (!($form = $this->getFormEntity())) {
            return new ViewModel();
        }

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $form);

        if (!$viewerMap) {
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to the given form!'
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'index',
                )
            );

            return new ViewModel();
        }

        $file = new CsvFile();

        $heading = array('ID', 'Submitter', 'Submitted');
        if ($viewerMap->isMail()) {
            $heading[] = 'Email';
        }

        if ($form->getType() == 'doodle') {
            $document = new DoodleGenerator($this->getEntityManager(), $viewerMap);
        } else {
            $document = new FormGenerator($this->getEntityManager(), $viewerMap, $this->getLanguage());
        }

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

        $fieldEntry = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findOneByValue($this->getParam('id'));

        if (null === $fieldEntry || !$this->getAuthentication()->isAuthenticated() || $fieldEntry->getFormEntry()->getCreationPerson() != $this->getAuthentication()->getPersonObject()) {
            return $this->notFoundAction();
        }

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $fieldEntry->getReadableValue() . '"',
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

    public function downloadFilesAction()
    {
        if (!($field = $this->getFieldEntity()) || $field->getType() != 'file') {
            return new ViewModel();
        }

        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByField($field);

        $tmpFile = new TmpFile();
        new ZipGenerator($tmpFile, $this->getEntityManager(), $this->getLanguage(), $entries);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="files_' . $field->getId() . '.zip"',
            'Content-Type'        => mime_content_type($tmpFile->getFileName()),
            'Content-Length'      => filesize($tmpFile->getFileName()),
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $tmpFile->getContent(),
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
                'form_manage',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $form->setEntityManager($this->getEntityManager());

        return $form;
    }

    /**
     * @return FormEntry|null
     */
    private function getEntryEntity()
    {
        $entry = $this->getEntityById('FormBundle\Entity\Node\Entry');

        if (!($entry instanceof FormEntry)) {
            $this->flashMessenger()->error(
                'Error',
                'No entry was found!'
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $entry;
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
                'form_manage',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $field;
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        return $this->getAuthentication()->getPersonObject();
    }
}
