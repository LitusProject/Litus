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

namespace BrBundle\Controller\Corporate;

use BrBundle\Entity\Company,
    BrBundle\Entity\Company\Job,
    BrBundle\Entity\Company\Request\RequestInternship,
    BrBundle\Form\Corporate\Internship\Add as AddForm,
    BrBundle\Form\Corporate\Internship\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    Zend\View\Model\ViewModel;

/**
 * InternshipController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class InternshipController extends \BrBundle\Component\Controller\CorporateController
{
    public function overviewAction()
    {
        $person = $this->getAuthentication()->getPersonObject();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Job')
                ->findAllActiveByCompanyAndTypeQuery($person->getCompany(), 'internship'),
            $this->getParam('page')
        );

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'logoPath' => $logoPath,
            )
        );
    }

    public function editAction()
    {
        if (!($oldJob = $this->_getJob()))
            return new ViewModel();

        $form = new EditForm($oldJob);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $contact = $this->getAuthentication()->getPersonObject();

                $job = new Job(
                    $formData['job_name'],
                    $formData['description'],
                    $formData['benefits'],
                    $formData['profile'],
                    $formData['contact'],
                    $formData['city'],
                    $contact->getCompany(),
                    'internship',
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $formData['sector']
                );

                $job->pending();

                $this->getEntityManager()->persist($job);

                $request = new RequestInternship($job, 'edit', $contact, $oldJob);

                $this->getEntityManager()->persist($request);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute(
                    'br_corporate_internship',
                    array(
                        'action' => 'overview',
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

    public function addAction()
    {
        $form = new AddForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $contact = $this->getAuthentication()->getPersonObject();

                $job = new Job(
                    $formData['job_name'],
                    $formData['description'],
                    $formData['benefits'],
                    $formData['profile'],
                    $formData['contact'],
                    $formData['city'],
                    $contact->getCompany(),
                    'internship',
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $formData['sector']
                );

                $job->pending();

                $this->getEntityManager()->persist($job);

                $request = new RequestInternship($job, 'add', $contact);

                $this->getEntityManager()->persist($request);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute(
                    'br_corporate_internship',
                    array(
                        'action' => 'overview',
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
        if (!($internship = $this->_getInternship()))
            return new ViewModel();

        $contact = $this->getAuthentication()->getPersonObject();

        $request = new RequestInternship($internship, 'delete', $contact);

        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getInternship()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the internship!'
            );

            $this->redirect()->toRoute(
                'br_corporate_internship',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $internship = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('internship', $this->getParam('id'));

        if (null === $internship) {
            $this->flashMessenger()->error(
                'Error',
                'No internship with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'br_corporate_internship',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $internship;
    }

    private function _getSectors()
    {
        $sectorArray = array();
        foreach (Company::$possibleSectors as $key => $sector)
            $sectorArray[$key] = $sector;

        return $sectorArray;
    }
}
