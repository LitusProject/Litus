<?php

namespace TicketBundle\Controller\Sale;

use Laminas\View\Model\ViewModel;

/**
 * PersonController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PersonController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function typeaheadAction()
    {
        $academicYear = $this->getCurrentAcademicYear();
        $persons = array_merge(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findAllByName($this->getParam('string'), $academicYear),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findAllByUniversityIdentification($this->getParam('string'), $academicYear),
            //            $this->getEntityManager()
            //                ->getRepository('CommonBundle\Entity\User\Person\Academic')
            //                ->findAllByBarcode($this->getParam('string'))
        );

        $result = array();
        foreach ($persons as $person) {
            $item = (object) array();
            $item->id = $person->getId();
            $item->value = $person->getUniversityIdentification() . ' - ' . $person->getFullName();

            if ($person->isMember($this->getCurrentAcademicYear())) {
                $item->value .= ' (Member)';
            }

            $result[] = $item;
        }
        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }
}
