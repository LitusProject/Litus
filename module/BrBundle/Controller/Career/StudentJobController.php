<?php

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Company\Job;
use Laminas\View\Model\ViewModel;

/**
 * Student Job Controller
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class StudentJobController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $studentJobSearchForm = $this->getForm('br_career_search_studentJob');

        $query = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByTypeByDateQuery('student job');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $studentJobSearchForm->setData($formData);

            if ($studentJobSearchForm->isValid()) {
                $formData = $studentJobSearchForm->getData();

                $repository = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company\Job');

                $sector = $formData['sector'] == 'all' ? null : $formData['sector'];
                $location = $formData['location'] == 'all' ? null : $formData['location'];
                $master = $formData['master'] == 'all' ? null : $formData['master'];

                if ($formData['searchType'] == 'company') {
                    $query = $repository->findAllActiveByTypeQuery('student job', $sector, $location, $master);
                } elseif ($formData['searchType'] == 'student_job') {
                    $query = $repository->findAllActiveByTypeSortedByJobNameQuery('student job', $sector, $location, $master);
                } elseif ($formData['searchType'] == 'mostRecent') {
                    $query = $repository->findAllActiveByTypeSortedByDateQuery('student job', $sector, $location, $master);
                }
            }
        }

        $paginator = $this->paginator()->createFromQuery(
            $query,
            $this->getParam('page')
        );

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'paginator'            => $paginator,
                'paginationControl'    => $this->paginator()->createControl(true),
                'logoPath'             => $logoPath,
                'studentJobSearchForm' => $studentJobSearchForm,
            )
        );
    }

    public function viewAction()
    {
        $studentJob = $this->getStudentJobEntity();
        if ($studentJob === null) {
            return new ViewModel();
        }

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'studentJob' => $studentJob,
                'logoPath'   => $logoPath,
            )
        );
    }

    /**
     * @return Job|null
     */
    private function getstudentJobEntity()
    {
        $job = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('student job', $this->getParam('id', 0));

        if (!($job instanceof Job)) {
            $this->flashMessenger()->error(
                'Error',
                'No job was found!'
            );

            $this->redirect()->toRoute(
                'br_career_student_job',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $job;
    }
}
