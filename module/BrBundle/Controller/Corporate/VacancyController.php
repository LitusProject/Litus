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

namespace BrBundle\Controller\Corporate;

use BrBundle\Entity\Company\Job;
use BrBundle\Entity\Company\Request\RequestVacancy;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

/**
 * VacancyController
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Incalza Dario <dario.incalza@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class VacancyController extends \BrBundle\Component\Controller\CorporateController
{
    public function overviewAction()
    {
        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Job')
                ->findAllActiveByCompanyAndTypeQuery($person->getCompany(), 'vacancy'),
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
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
                'logoPath'           => $logoPath,
                'requests'           => $requests,
                'unfinishedRequests' => $unfinishedRequestsJobs,
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('br_corporate_job_add');

        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $job = $form->hydrateObject(
                    new Job($person->getCompany(), 'vacancy')
                );

                $job->pending();

                $this->getEntityManager()->persist($job);

                $request = new RequestVacancy($job, 'add', $person);

                $this->getEntityManager()->persist($request);
                $this->getEntityManager()->flush();

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.vacancy_mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.vacancy_mail_name');

                $link = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.vacancy_link');

                $mail = new Message();
                $mail->setBody($link)
                    ->setFrom($mailAddress, $mailName)
                    ->addTo($mailAddress, $mailName)
                    ->setSubject('New Vacancy Request ' . $person->getCompany()->getName());

                if (getenv('APPLICATION_ENV') != 'development') {
                    $this->getMailTransport()->send($mail);
                }

                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute(
                    'br_corporate_vacancy',
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
        if (!($oldJob = $this->getVacancyEntity())) {
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
                'br_corporate_vacancy',
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
                        new Job($person->getCompany(), 'vacancy')
                    );
                    $job->pending();
                    $this->getEntityManager()->persist($job);

                    $request = new RequestVacancy($job, 'edit', $person, $oldJob);
                    $this->getEntityManager()->persist($request);
                } else {
                    $job = $form->hydrateObject($oldJob);
                    $this->getEntityManager()->persist($job);

                    $unhandledRequest = $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Company\Request\RequestVacancy')
                        ->findUnhandledRequestsByJob($oldJob);

                    if (empty($unhandledRequest)) {
                        $oldRequest = $this->getEntityManager()
                            ->getRepository('BrBundle\Entity\Company\Request\RequestVacancy')
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
                    'br_corporate_vacancy',
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
        if (!($vacancy = $this->getVacancyEntity())) {
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

        if (isset($unfinishedRequestsJobs[$vacancy->getId()])) {
            $this->redirect()->toRoute(
                'br_corporate_vacancy',
                array(
                    'action' => 'overview',
                )
            );
        }

        $request = new RequestVacancy($vacancy, 'delete', $person);

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
                'br_corporate_vacancy',
                array(
                    'action' => 'overview',
                )
            );
        }

        $request = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\RequestVacancy')
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
    private function getVacancyEntity()
    {
        $job = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('vacancy', $this->getParam('id', 0));

        if (!($job instanceof Job)) {
            $this->flashMessenger()->error(
                'Error',
                'No job was found!'
            );

            $this->redirect()->toRoute(
                'br_career_vacancy',
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
            ->getRepository('BrBundle\Entity\Company\Request\RequestVacancy')
            ->findOneById($this->getParam('id', 0));

        if (!($request instanceof RequestVacancy)) {
            $this->flashMessenger()->error(
                'Error',
                'No request was found!'
            );

            $this->redirect()->toRoute(
                'br_career_vacancy',
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
    private function getOpenRequests($company)
    {
        $unhandledRequests = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\RequestVacancy')
            ->findAllUnhandledByCompany($company);

        $handledRejects = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\RequestVacancy')
            ->findRejectsByCompany($company);

        return array_merge($handledRejects, $unhandledRequests);
    }
}
