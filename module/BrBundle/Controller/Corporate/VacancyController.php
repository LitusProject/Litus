<?php

namespace BrBundle\Controller\Corporate;

use BrBundle\Entity\Company\Job;
use BrBundle\Entity\Company\Request\Vacancy;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

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
        $person = $this->getCorporateEntity();
        if ($person === null) {
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
                $unfinishedRequestsJobs[$request->getJob()->getId()] = $request->getRequestType();
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
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_corporate_job_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $job = $form->hydrateObject(
                    new Job($person->getCompany(), 'vacancy')
                );

                $job->pending();

                $this->getEntityManager()->persist($job);

                $request = new Vacancy($job, 'add', $person);

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
        $oldJob = $this->getVacancyEntity();
        if ($oldJob === null) {
            return new ViewModel();
        }

        $person = $this->getCorporateEntity();
        if ($person === null) {
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

                    $request = new Vacancy($job, 'edit', $person, $oldJob);
                    $this->getEntityManager()->persist($request);
                } else {
                    $job = $form->hydrateObject($oldJob);
                    $this->getEntityManager()->persist($job);

                    $unhandledRequest = $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Company\Request\Vacancy')
                        ->findUnhandledRequestsByJob($oldJob);

                    if (count($unhandledRequest) == 0) {
                        $oldRequest = $this->getEntityManager()
                            ->getRepository('BrBundle\Entity\Company\Request\Vacancy')
                            ->findOneByJob($oldJob->getId());

                        $request = new Vacancy($job, 'edit reject', $person, $oldRequest->getEditJob());
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
        $vacancy = $this->getVacancyEntity();
        if ($vacancy === null) {
            return new ViewModel();
        }

        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $vacancy->remove();
        $this->getEntityManager()->persist($vacancy);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteRequestAction()
    {
        $request = $this->getRequestEntity();
        if ($request === null) {
            $this->redirect()->toRoute(
                'br_corporate_vacancy',
                array(
                    'action' => 'overview',
                )
            );
        }

        $request = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\Vacancy')
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
            ->findOneActiveById($this->getParam('id', 0));

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
     * @return Vacancy|null
     */
    private function getRequestEntity()
    {
        $request = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\Vacancy')
            ->findOneById($this->getParam('id', 0));

        if (!($request instanceof Vacancy)) {
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
            ->getRepository('BrBundle\Entity\Company\Request\Vacancy')
            ->findAllUnhandledByCompany($company);

        $handledRejects = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\Vacancy')
            ->findRejectsByCompany($company);

        return array_merge($handledRejects, $unhandledRequests);
    }
}
