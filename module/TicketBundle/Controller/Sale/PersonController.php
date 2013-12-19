<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace TicketBundle\Controller\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * PersonController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PersonController extends \TicketBundle\Component\Controller\SaleController
{
    public function typeaheadAction()
    {
        $persons = array_merge(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findAllByName($this->getParam('string')),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findAllByUniversityIdentification($this->getParam('string')),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findAllByBarcode($this->getParam('string'))
        );

        $result = array();
        foreach($persons as $person) {
            $item = (object) array();
            $item->id = $person->getId();
            $item->value = $person->getUniversityIdentification() . ' - ' . $person->getFullName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }
}
