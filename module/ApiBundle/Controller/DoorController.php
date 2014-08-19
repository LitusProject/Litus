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

namespace ApiBundle\Controller;

use CommonBundle\Entity\User\Person\Academic,
    DoorBundle\Document\Log,
    DoorBundle\Document\Rule,
    Zend\View\Model\ViewModel;

/**
 * DoorController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class DoorController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function getRulesAction()
    {
        $this->initJson();

        $result = array();

        $statuses = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Status\Organization')
            ->findAllByStatus('praesidium', $this->getCurrentAcademicYear());

        foreach ($statuses as $status) {
            $identification = $status->getPerson()->getUniversityIdentification();
            if (!isset($result[$identification]))
                $result[$identification] = array();

            $result[$identification][] = array(
                'academic' => $status->getPerson()->getId(),
                'start_date' => null,
                'end_date' => null,
                'start_time' => 0,
                'end_time' => 0,
            );
        }

        $rules = $this->getDocumentManager()
            ->getRepository('DoorBundle\Document\Rule')
            ->findAll();

        foreach ($rules as $rule) {
            $identification = $rule->getAcademic($this->getEntityManager())->getUniversityIdentification();
            if (!isset($result[$identification]))
                $result[$identification] = array();

            $result[$identification][] = array(
                'academic' => $rule->getAcademic($this->getEntityManager())->getId(),
                'start_date' => $rule->getStartDate()->format('U'),
                'end_date' => $rule->getEndDate()->format('U'),
                'start_time' => $rule->getStartTime(),
                'end_time' => $rule->getEndTime(),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    public function logAction()
    {
        $this->initJson();

        if (!($academic = $this->_getAcademic()))
            return $this->error(500, 'The person does not exist');

        $log = new Log($academic);

        $this->getDocumentManager()->persist($log);
        $this->getDocumentManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Academic|null
     */
    private function _getAcademic()
    {
        if (null !== $this->getRequest()->getPost('academic')) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($this->getRequest()->getPost('academic'));
        }

        return null;
    }
}
