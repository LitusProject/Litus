<?php

namespace ApiBundle\Controller;

use BrBundle\Entity\Company;
use CommonBundle\Entity\General\AcademicYear;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\View\Model\ViewModel;

/**
 * BrController
 */
class BrController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    /**
     * input: json {
     *      "key": "api key",
     *      "company": "company name",
     * }
     */
    public function AddCompanyAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $input = $this->getRequest()->getContent();
        $json = json_decode($input);

        $company_name = $json->company;

        $company = new Company();
        $company->setName($company_name);

        $this->getEntityManager()->persist($company);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * input: json {
     *      "key": "api key",
     *      "company": "old company name",
     *      "new_name": "new company name"
     * }
     */
    public function editCompanyNameAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $input = $this->getRequest()->getContent();
        $json = json_decode($input);

        $company_name = $json->company;
        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findallByNameQuery($company_name)
            ->getResult()[0];

        $new_name = $json->new_name;

        $company->setName($new_name);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * input: json {
     *      "key": "api key",
     *      "company": "company name",
     *      "year": "xxxx-yyyy"
     * }
     */
    public function addCvBookAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $input = $this->getRequest()->getContent();
        $json = json_decode($input);

        $company_name = $json->company;
        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findallByNameQuery($company_name)
            ->getResult()[0];

        $year = $json->year;
        $academic_year = $this->getAcademicYear($year);
        $company->addCvBookYear($academic_year);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * input: json {
     *      "key": "api key",
     *      "company": "company name"
     * }
     */
    public function addPageVisibleAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $input = $this->getRequest()->getContent();
        $json = json_decode($input);

        $company_name = $json->company;

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findallByNameQuery($company_name)
            ->getResult()[0];

        $page = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Page')
            ->findById($company->getId())[0];

        $page->addYear($this->getCurrentAcademicYear());

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * input: json {
     *      "key": "api key",
     *      "company": "company name"
     * }
     */
    public function getCvYearsAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $input = $this->getRequest()->getContent();
        $json = json_decode($input);

        $company_name = $json->company;

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findallByNameQuery($company_name)
            ->getResult()[0];

        $cv_years = $company->getCvBookYears();
        $years_array = new ArrayCollection();
        foreach ($cv_years as $year)
        {
            $years_array->add($year->getCode());
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    "years"  => (object) $years_array->toArray(),
                ),
            )
        );
    }

    /**
     * @param string $year The year for which to add a CV book, notation: xxxx-yyyy
     * @return AcademicYear|null
     *
     * To Do: Misschien ooit mooier/beter schrijven, want zeer inefficient nu
     */
    private function getAcademicYear(string $year)
    {
        $all_academic_years = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();
        foreach ($all_academic_years as $academic_year)
        {
            $code = $academic_year->getCode();
            if ($code == $year)
                return $academic_year;
        }
        return null;
    }
}