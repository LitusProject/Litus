<?php

namespace ApiBundle\Controller;

use CommonBundle\Component\Controller\ActionController;
use CommonBundle\Entity\User\Person\Academic;
use DoorBundle\Entity\Log;
use Laminas\View\Model\ViewModel;

/**
 * DoorController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class DoorController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function rulesAction()
    {
        $this->initJson();

        $result = array();

        $statuses1 = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Status\Organization')
            ->findAllByStatus('praesidium', $this->getCurrentAcademicYear());

        $statuses2 = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Status\Organization')
            ->findAllByStatus('praesidium', $this->getCurrentAcademicYear(true));

        $statuses = array_merge($statuses1, $statuses2);

        foreach ($statuses as $status) {
            $identification = $status->getPerson()->getUniversityIdentification();
            if (!isset($result[$identification])) {
                $result[$identification] = array();
            }

            $result[$identification][] = array(
                'academic'   => $status->getPerson()->getId(),
                'start_date' => null,
                'end_date'   => null,
                'start_time' => 0,
                'end_time'   => 0,
            );
        }

        $rules = $this->getEntityManager()
            ->getRepository('DoorBundle\Entity\Rule')
            ->findAll();

        foreach ($rules as $rule) {
            $identification = $rule->getAcademic($this->getEntityManager())->getUniversityIdentification();
            if (!isset($result[$identification])) {
                $result[$identification] = array();
            }

            $result[$identification][] = array(
                'academic'   => $rule->getAcademic($this->getEntityManager())->getId(),
                'start_date' => $rule->getStartDate()->format('U'),
                'end_date'   => $rule->getEndDate()->format('U'),
                'start_time' => $rule->getStartTime(),
                'end_time'   => $rule->getEndTime(),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result,
            )
        );
    }

    public function getRulesAction()
    {
        return $this->rulesAction();
    }

    public function logAction()
    {
        $this->initJson();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return $this->error(404, 'The person does not exist');
        }

        $log = new Log($academic);

        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function getUsernameAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }
        $userData = $this->getRequest()->getPost('userData');
        $seperatedString = explode(';', $userData);

        if ($seperatedString[1] === '') {
            return new ViewModel(
                array(
                    'result' => (object) array(
                        'status' => 'error',
                        'reason' => 'toShort',
                    ),
                ),
            );
        }

        $actionController = new ActionController();
        $rNumber = $actionController->getRNumberAPI($seperatedString[0], $seperatedString[1], $this->getEntityManager());

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'person' => $rNumber,
                ),
            ),
        );
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if ($this->getRequest()->getPost('academic') !== null) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($this->getRequest()->getPost('academic'));
        }

        return null;
    }
}
