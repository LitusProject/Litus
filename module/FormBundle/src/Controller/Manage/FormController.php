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

namespace FormBundle\Controller\Manage;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    FormBundle\Component\Document\Generator\Csv as CsvGenerator,
    FormBundle\Entity\Entry as FieldEntry,
    FormBundle\Form\SpecifiedForm,
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
            ->getRepository('FormBundle\Entity\Nodes\Entry')
            ->findAllByForm($form);

        return new ViewModel(
            array(
                'form'    => $form,
                'fields'  => $fields,
                'entries' => $entries,
                'viewer'  => $viewerMap,
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

        $form = new SpecifiedForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $formEntry->getCreationPerson());
        $form->populateFromEntry($formEntry);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                foreach ($formSpecification->getFields() as $field) {

                    $value = $formData['field-' . $field->getId()];

                    // Find entry
                    $fieldEntry = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Entry')
                        ->findOneByFormEntryAndField($formEntry, $field);

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
        $fields = $form->getFields();
        foreach ($fields as $field) {
            $heading[] = $field->getLabel($language);
        }

        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Nodes\Entry')
            ->findAllByForm($form);

        $results = array();
        foreach ($entries as $entry) {
            $result = array($entry->getId(), $entry->getPersonInfo()->getFirstName() . ' ' . $entry->getPersonInfo()->getLastName(), $entry->getCreationTime()->format('d/m/Y H:i'));
            if ($viewerMap->isMail()) {
                $result[] = $entry->getPersonInfo()->getEmail();
            }
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

        $document = new CsvGenerator($this->getEntityManager(), $heading, $results);
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
            ->getRepository('FormBundle\Entity\Nodes\Form')
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
            ->getRepository('FormBundle\Entity\Nodes\Entry')
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
