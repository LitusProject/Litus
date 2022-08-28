<?php

namespace ApiBundle\Controller;

use BrBundle\Entity\Company;
use BrBundle\Entity\User\Person\Corporate as CorporateEntity;
use CommonBundle\Entity\General\AcademicYear;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\View\Model\ViewModel;

/**
 * BrController
 */
class BrController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    /**
     * input: {
     *      "key": "api key",
     *      "company": "company name",
     * }
     */
    public function addCompanyAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $company_name = $this->getRequest()->getPost('company');

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
     * input: {
     *      "key": "api key",
     *      "company": "company id",
     *      "new_name": "new company name"
     * }
     */
    public function editCompanyNameAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $company_id = $this->getRequest()->getPost('company');

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findById($company_id)[0];

        $new_name = $this->getRequest()->getPost('new_name');

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
     * input: {
     *      "key": "api key",
     *      "company": "company id",
     *      "year": "xxxx-yyyy"
     * }
     */
    public function addCvBookAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $company_id = $this->getRequest()->getPost('company');

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findById($company_id)[0];

        $year = $this->getRequest()->getPost('year');

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
     * input: {
     *      "key": "api key",
     *      "company": "company id"
     * }
     */
    public function addPageVisibleAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $company_id = $this->getRequest()->getPost('company');

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findById($company_id)[0];

        $page = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Page')
            ->findOneBy(array(
                'company' => $company->getId()));

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
     * input: {
     *      "key": "api key",
     *      "company": "company id"
     * }
     */
    public function isPageVisibleAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $company_id = $this->getRequest()->getPost('company');

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findById($company_id)[0];

        $page = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Page')
            ->findOneBy(
                array(
                    'company' => $company->getId()
                )
            );

        $current_academic_year = $this->getCurrentAcademicYear();

        $is_visible = in_array($current_academic_year, $page->getYears());

        return new ViewModel(
            array(
                'result' => (object) array(
                    'visible' => $is_visible,
                ),
            )
        );
    }

    /**
     * input: {
     *      "key": "api key",
     *      "company": "company id"
     * }
     */
    public function getCvYearsAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $company_id = $this->getRequest()->getPost('company');
        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findById($company_id)[0];

        $cv_years = $company->getCvBookYears();
        $years_array = new ArrayCollection();

        foreach ($cv_years as $year) {
            $years_array->add($year->getCode());
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'years'  => (object) $years_array->toArray(),
                ),
            )
        );
    }

    /**
     * input: {
     *      "key": "api key",
     *      "company": "company name"
     * }
     */
    public function getCompanyIdAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $company_name = $this->getRequest()->getPost('company');

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findallByNameQuery($company_name)
            ->getResult()[0];

        $company_id = $company->getId();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'id'  => $company_id,
                ),
            )
        );
    }

    /**
     * input: {
     *      "key": "api key",
     *      "user": "user id"
     * }
     */
    public function sendActivationAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $user_id = $this->getRequest()->getPost('user');

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($user_id);
        if (!is_null($person)) {
            $person->activate(
                $this->getEntityManager(),
                $this->getMailTransport(),
                false,
                'br.account_activated_mail',
                86400 * 30
            );
            return new ViewModel(
                array(
                    'result' => (object)array(
                        'status' => 'success',
                    ),
                )
            );
        } else {
            return $this->error(400, 'The user is not found');
        }
    }

    /**
     * input: {
     *      "key": "api key",
     *      "user_name": "user name"
     *      "first_name": "first name"
     *      "last_name": "last name"
     *      "email": "email"
     *      "sex": "m/f/null"
     *      "company": "company id"
     * }
     */
    public function addUserAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $person = new CorporateEntity();
        $person->setUsername($this->getRequest()->getPost('user_name'));
        $person->setFirstName($this->getRequest()->getPost('first_name'));
        $person->setLastName($this->getRequest()->getPost('last_name'));
        $person->setEmail($this->getRequest()->getPost('email'));
        //$person->setSex($this->getRequest()->getPost("sex"));

        $person->setRoles(array_unique(array_merge($this->dataToRoles(array('corporate')), $person->getSystemRoles())));

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getRequest()->getPost('company'));

        $person->setCompany($company);

        $this->getEntityManager()->persist($person);
        $this->getEntityManager()->flush();

        $person->activate(
            $this->getEntityManager(),
            $this->getMailTransport(),
            false,
            'br.account_activated_mail',
            86400 * 30
        );

        return new ViewModel(
            array(
                'result' => (object)array(
                    'status' => 'success',
                    'id'     => $person->getId(),
                ),
            )
        );
    }

    /**
     * input: {
     *      "key": "api key",
     *      "user_name": "user name"
     * }
     */
    public function getUserIdAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $user_name = $this->getRequest()->getPost('user_name');

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneByUsername($user_name);

        return new ViewModel(
            array(
                'result' => (object)array(
                    'status' => 'success',
                    'id'     => $person->getId(),
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
        foreach ($all_academic_years as $academic_year) {
            $code = $academic_year->getCode();
            if ($code == $year) {
                return $academic_year;
            }
        }
        return null;
    }

    protected function dataToRoles($rolesData)
    {
        $roles = array();

        foreach ($rolesData as $role) {
            $roles[] = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Role')
                ->findOneByName($role);
        }

        return $roles;
    }
}
