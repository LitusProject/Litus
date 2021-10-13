<?php

namespace CommonBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;

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
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findAllByNameQuery($this->getParam('string'))
            ->setMaxResults(20)
            ->getResult();

        $result = array();
        foreach ($persons as $person) {
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
