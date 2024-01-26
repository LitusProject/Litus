<?php

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Company\Page;
use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * CompanyController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CompanyController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        $pages = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Page')
            ->findAllActiveQuery($this->getCurrentAcademicYear())->getResult();

        $smallComps = array();
        $largeComps = array();
        foreach ($pages as $page) {
            $item = (object) array();
            $company = $page->getCompany();

            $vacancies = count(
                $this->getEntityManager()->getRepository('BrBundle\Entity\Company\Job')
                    ->findAllActiveByCompanyAndTypeQuery($company, 'vacancy')->getResult()
            );
            $internships = count(
                $this->getEntityManager()->getRepository('BrBundle\Entity\Company\Job')
                    ->findAllActiveByCompanyAndTypeQuery($company, 'internship')->getResult()
            );
            $studentJobs = count(
                $this->getEntityManager()->getRepository('BrBundle\Entity\Company\Job')
                    ->findAllActiveByCompanyAndTypeQuery($company, 'student job')->getResult()
            );

            $item->name = $company->getName();
            $item->logo = $company->getLogo();
            $item->slug = $company->getSlug();
            $item->vacancies = $vacancies;
            $item->internships = $internships;
            $item->studentJobs = $studentJobs;
            if ($page->getCompany()->isLarge() == false) {
                $item->large = 0;
                $smallComps[] = $item;
            } else {
                $item->large = 1;
                $item->description = $page->getShortDescription();
                $largeComps[] = $item;
            }
        }
        shuffle($smallComps);
        shuffle($largeComps);
        $allComps = array();

        $nbLargeComps = count($largeComps);
        $nbSmallComps = count($smallComps);
        while ($nbLargeComps > 0 || $nbSmallComps > 0) {
            if ($nbLargeComps > 0) {
                $comp = array_pop($largeComps);
                array_push($allComps, $comp);

                $nbLargeComps--;
            }
            if ($nbSmallComps >= 4) {
                $comp = array_splice($smallComps, 0, 4);
                array_push($allComps, $comp);

                $nbSmallComps = count($smallComps);
            } else {
                array_push($allComps, $smallComps);

                $smallComps = array();
                $nbSmallComps = 0;
            }
        }

        return new ViewModel(
            array(
                'logoPath'       => $logoPath,
                'smallCompanies' => $smallComps,
                'largeCompanies' => $largeComps,
                'allCompanies'   => $allComps,
                // 'possible_sectors' => array('all' => 'All') + Company::POSSIBLE_SECTORS,
            )
        );
    }

    public function viewAction()
    {
        $page = $this->getPageEntity();
        if ($page === null) {
            return new ViewModel();
        }

        $language = $this->getLanguage();

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        $events = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Event')
            ->findAllFutureByCompany(new DateTime(), $page->getCompany());

        $internships = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByCompanyAndType($page->getCompany(), 'internship');

        $vacancies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByCompanyAndType($page->getCompany(), 'vacancy');

        return new ViewModel(
            array(
                'logoPath'    => $logoPath,
                'page'        => $page,
                'events'      => $events,
                'internships' => $internships,
                'vacancies'   => $vacancies,
                'language'    => $language,
            )
        );
    }

    public function fileAction()
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.file_path') . '/' . $this->getParam('name');

        if ($this->getParam('name') == '' || !file_exists($filePath)) {
            return $this->notFoundAction();
        }

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'inline; filename="' . $this->getParam('name') . '"',
                'Content-Type'        => mime_content_type($filePath),
                'Content-Length'      => filesize($filePath),
            )
        );
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

    public function searchAction()
    {
        $this->initAjax();

        $result = array();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            $pages = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Page')
                ->findAllActiveBySearch($this->getCurrentAcademicYear(), $data['query'], $data['sector']);

            foreach ($pages as $page) {
                $item = (object) array();
                $item->name = $page->getCompany()->getName();
                $item->logo = $page->getCompany()->getLogo();
                $item->slug = $page->getCompany()->getSlug();
                $result[] = $item;
            }
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Page|null
     */
    private function getPageEntity()
    {
        $page = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Page')
            ->findOneActiveBySlug($this->getParam('company', 0), $this->getCurrentAcademicYear());

        if (!($page instanceof Page)) {
            $this->flashMessenger()->error(
                'Error',
                'No page was found!'
            );

            $this->redirect()->toRoute(
                'br_career_company',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $page;
    }
}
