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
    BrBundle\Entity\User\Person\Corporate,
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
        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Job')
                ->findAllActiveByCompanyAndTypeQuery($person->getCompany(), 'internship'),
            $this->getParam('page')
        );

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        $requests = $this->getOpenRequests($person->getCompany());

        $unfinishedRequestsJobs = array();
        foreach ($requests as $request) {
            if ($request->getRequestType() == 'edit' || $request->getRequestType() == 'edit reject') {
                $unfinishedRequestsJobs[$request->getEditJob()->getId()] = $request->getRequestType();
            } else {
                $unfinishedRequestsJobs[$request->getJob()->getId()] = $request->getRequestType();
            }
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'logoPath' => $logoPath,
                'requests' => $requests,
                'unfinishedRequests' => $unfinishedRequestsJobs,
            )
        );
    }

    public function addAction()
    {
        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('br_corporate_job_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $job = $form->hydrateObject(
                    new Job($person->getCompany(), 'internship')
                );

                $job->pending();

                $this->getEntityManager()->persist($job);

                $request = new RequestInternship($job, 'add', $person);

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

    public function editAction()
    {
        if (!($oldJob = $this->getInternshipEntity())) {
            return new ViewModel();
        }

        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        $requests = $this->getOpenRequests($person->getCompany());

        $unfinishedRequestsJobs = array();
        foreach ($requests as $request) {
            if ($request->getRequestType() == 'edit' || $request->getRequestType() == 'edit reject') {
                $unfinishedRequestsJobs[$request->getEditJob()->getId()] = $request->getRequestType();
            } elseif ($request->getRequestType() == 'delete') {
                $unfinishedRequestsJobs[$request->getJob()->getId()] = $request->getRequestType();
            }
        }

        if (isset($unfinishedRequestsJobs[$oldJob->getId()])) {
            $this->redirect()->toRoute(
                'br_corporate_internship',
                array(
                    'action' => 'overview',
                )
            );
        }

        $form = $this->getForm('br_corporate_job_edit', array('job' => $oldJob));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                if ($oldJob->isApproved()) {
                    $job = $form->hydrateObject(
                        new Job($person->getCompany(), 'internship')
                    );
                    $job->pending();
                    $this->getEntityManager()->persist($job);

                    $request = new RequestInternship($job, 'edit', $person, $oldJob);
                    $this->getEntityManager()->persist($request);
                } else {
                    $job = $form->hydrateObject($oldJob);
                    $this->getEntityManager()->persist($job);

                    $unhandledRequest = $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Company\Request\RequestInternship')
                        ->findUnhandledRequestsByJob($oldJob);

                    if (empty($unhandledRequest)) {
                        $oldRequest = $this->getEntityManager()
                            ->getRepository('BrBundle\Entity\Company\Request\RequestInternship')
                            ->findOneByJob($oldJob->getId());

                        $request = new RequestVacancy($job, 'edit reject', $person, $oldRequest->getEditJob());
                        $this->getEntityManager()->persist($request);

                        if (isset($oldRequest)) {
                            $this->getEntityManager()->remove($oldRequest);
                        }
                    }
                }

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
        if (!($internship = $this->getInternshipEntity())) {
            return new ViewModel();
        }

        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        $requests = $this->getOpenRequests($person->getCompany());

        $unfinishedRequestsJobs = array();
        foreach ($requests as $request) {
            if ($request->getRequestType() == 'edit' || $request->getRequestType() == 'edit reject') {
                $unfinishedRequestsJobs[$request->getEditJob()->getId()] = $request->getRequestType();
            } else {
                $unfinishedRequestsJobs[$request->getJob()->getId()] = $request->getRequestType();
            }
        }

        if (isset($unfinishedRequestsJobs[$internship->getId()])) {
            $this->redirect()->toRoute(
                'br_corporate_internship',
                array(
                    'action' => 'overview',
                )
            );
        }

        $request = new RequestInternship($internship, 'delete', $person);

        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteRequestAction()
    {
        if (!($request = $this->getRequestEntity())) {
            $this->redirect()->toRoute(
                'br_corporate_internship',
                array(
                    'action' => 'overview',
                )
            );
        }

        $request = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\RequestInternship')
            ->findOneById($request->getId());

        $this->getEntityManager()->remove($request);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Job|null
     */
    private function getInternshipEntity()
    {
        $job = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('internship', $this->getParam('id', 0));

        if (!($job instanceof Job)) {
            $this->flashMessenger()->error(
                'Error',
                'No internship was found!'
            );

            $this->redirect()->toRoute(
                'br_career_internship',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $job;
    }

    /**
     * @return RequestVacancy|null
     */
    private function getRequestEntity()
    {
        $request = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\RequestInternship')
            ->findOneById($this->getParam('id', 0));

        if (!($request instanceof RequestInternship)) {
            $this->flashMessenger()->error(
                'Error',
                'No request was found!'
            );

            $this->redirect()->toRoute(
                'br_career_internship',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $request;
    }

    /**
     * @return array
     */
    private function getSectors()
    {
        $sectorArray = array();
        foreach (Company::$possibleSectors as $key => $sector) {
            $sectorArray[$key] = $sector;
        }

        return $sectorArray;
    }

    /**
     * @return array
     */
    private function getOpenRequests($company)
    {
        $unhandledRequests = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\RequestInternship')
            ->findAllUnhandledByCompany($company);

        $handledRejects = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\RequestInternship')
            ->findRejectsByCompany($company);

        return array_merge($handledRejects, $unhandledRequests);
    }
}
