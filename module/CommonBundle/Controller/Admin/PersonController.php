<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\Users\Person,
    Zend\View\Model\ViewModel;

/**
 * PersonController
 *
 * @autor Niels Avonds <niels.avonds@litus.cc>
 */
class PersonController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function typeaheadAction()
    {
        $this->initAjax();

        $persons = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Person')
            ->findAllByNameTypeahead($this->getParam('string'));

        $result = array();
        foreach($persons as $person) {
            $item = (object) array();
            $item->id = $person->getId();
            $item->value = $person->getFullName() . ' - ' . $person->getUsername();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }
}
