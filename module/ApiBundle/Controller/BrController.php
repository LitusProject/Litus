<?php

namespace ApiBundle\Controller;

use BrBundle\Entity\Company;
use BrBundle\Entity\User\Person\Corporate as CorporateEntity;
use BrBundle\Entity\Event as EventEntity;
use BrBundle\Entity\Event\Subscription as SubscriptionEntity;
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

        $id = $company->getId();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'id'     => $id,
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
     *      "sex": "m/f/x/null"
     *      "company": "company id"
     * }
     */
    public function addUserAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }


        $person = new CorporateEntity();
        try {
            $person->setUsername($this->getRequest()->getPost('user_name'));
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $person->setFirstName($this->getRequest()->getPost('first_name'));
        $person->setLastName($this->getRequest()->getPost('last_name'));
        $person->setEmail($this->getRequest()->getPost('email'));
        //$person->setSex($this->getRequest()->getPost("sex"));

        $person->setRoles(array_unique(array_merge($this->dataToRoles(array('corporate')), $person->getSystemRoles())));

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getRequest()->getPost('company'));

        $person->setCompany($company);

        try {
            $this->getEntityManager()->persist($person);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $error_mes = $e->getMessage();
            if (str_contains($error_mes, "already exists") and str_contains($error_mes, "Key (username)")) {
                return new ViewModel(
                    array(
                        'result' => (object)array(
                            'status' => 'error',
                            'reason' => 'duplicate key',
                        ),
                    )
                );
            }

        }

// To activate the person automatically on creation, use this:
//        $person->activate(
//            $this->getEntityManager(),
//            $this->getMailTransport(),
//            false,
//            'br.account_activated_mail',
//            86400 * 90
//        );

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
     * This API endpoint serves as an endpoint for the MIXX printers at the entrance of the jobfair.
     * At this endpoint, one gets a list of all subscriptions since a given date, if properly authenticated.
     *
     * URL: vtk.be/api/br/getSubscriptions?key=apiKey&event=eventId&page=pageNumber&length=pageLength
     * headers:
     *      Event: eventId (same as event param) (optional)
     *      Last-ID: lastId (optional)
     * query:
     *      key=apiKey
     *      event=eventId (same as Event header) (optional)
     *      page=pageNumber (optional)
     *      length=pageLength (optional)
     */
    public function getSubscriptionsAction()
    {
        $this->initJson();

        // Get Event ID from request, either through a query param or a header
        // Probably way to complicated to do this

        $eventIdParam = $this->getRequest()->getQuery('event');
        $eventIdHeader = $this->getRequest()->getHeaders()->get('Event') ? $this->getRequest()->getHeaders()->get('Event')->getFieldValue() : null;

        if ($eventIdParam == null && $eventIdHeader == null) {
            return $this->error(400, 'No event ID was supplied');
        } elseif ($eventIdParam !== null && $eventIdHeader !== null) {
            if ($eventIdParam != $eventIdHeader) {
                return $this->error(400, 'The Get Event param and Header event param are different');
            } else {
                $event = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Event')
                    ->findOneById($eventIdHeader);
            }
        } elseif ($eventIdParam == null) {
            $event = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event')
                ->findOneById($eventIdHeader);
        } else {
            $event = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event')
                ->findOneById($eventIdParam);
        }

        if (is_null($event)) {
            return $this->error(404, 'The event was not found');
        }

        // Get subscriptions for this event
        // Check first if a previous ID is given.
        // If this is the case, get subscriptions with higher ID

        $lastId = $this->getRequest()->getHeaders()->get('Last-ID') ? $this->getRequest()->getHeaders()->get('Last-ID')->getFieldValue() : null;

        if ($lastId == null) {
            $allSubscriptions = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event\Subscription')
                ->findAllByEventQuery($event)
                ->getResult();
        } else {
            $allSubscriptions = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event\Subscription')
                ->findAllByEventAndStartingIDQuery($event, $lastId)
                ->getResult();
        }


        if (is_null($allSubscriptions)) {
            return $this->error(404, 'No subscriptions were found');
        }

        // Get page number from query
        $pageNumber = $this->getRequest()->getQuery('page');
        if (is_null($pageNumber)) {
            $pageNumber = 1;
        }

        // Get page length from query
        $pageLength = $this->getRequest()->getQuery('length');
        if (is_null($pageLength)) {
            $pageLength = 100;
        }

        // Create Response
        $startingIndex = ($pageNumber-1)*$pageLength;

        $result = array();
        $slice = array_slice($allSubscriptions, $startingIndex, $pageLength);

        foreach ($slice as $subscription) {
            $url = $this->url()
                ->fromRoute(
                    'br_career_event',
                    array('action' => 'qr',
                        'id'       => $event->getId(),
                        'code'     => $subscription->getQrCode()
                    ),
                    array('force_canonical' => true)
                );
            $url = str_replace('leia.', '', $url);
            $url = str_replace('liv.', '', $url);

            $result[] = array(
                'id' => $subscription->getId(),
                'name' => $subscription->getFirstName() . ' ' . $subscription->getLastName(),
                'study' => $subscription->getStudy(),
                'url' => $url,
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result,
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
