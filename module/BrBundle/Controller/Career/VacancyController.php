<?php

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Company\Job;
use Laminas\View\Model\ViewModel;

/**
 * VacancyController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class VacancyController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $vacancySearchForm = $this->getForm('br_career_search_vacancy');

        $query = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByDateQuery();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $vacancySearchForm->setData($formData);

            if ($vacancySearchForm->isValid()) {
                $formData = $vacancySearchForm->getData();

                $repository = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company\Job');

                $jobType = $formData['jobType'] == 'all' ? null : $formData['jobType'];

                $sector = $formData['sector'] == 'all' ? null : $formData['sector'];
                $location = $formData['location'] == 'all' ? null : $formData['location'];
                $master = $formData['master'] == 'all' ? null : $formData['master'];

                if ($formData['searchType'] == 'company') {
                    $query = $repository->findAllActiveByTypeQuery($jobType, $sector, $location, $master);
                } elseif ($formData['searchType'] == 'vacancy') {
                    $query = $repository->findAllActiveByTypeSortedByJobNameQuery($jobType, $sector, $location, $master);
                } elseif ($formData['searchType'] == 'mostRecent') {
                    $query = $repository->findAllActiveByTypeSortedByDateQuery($jobType, $sector, $location, $master);
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
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'logoPath'          => $logoPath,
                'vacancySearchForm' => $vacancySearchForm,
                'fathom'            => $this->getFathomInfo(),
            )
        );
    }

    public function viewAction()
    {
        $vacancy = $this->getVacancyEntity();
        if ($vacancy === null) {
            return new ViewModel();
        }

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'vacancy'  => $vacancy,
                'logoPath' => $logoPath,
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
}
