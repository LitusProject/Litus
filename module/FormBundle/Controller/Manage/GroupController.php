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

namespace FormBundle\Controller\Manage;

use Zend\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \FormBundle\Component\Controller\FormController
{
    public function indexAction()
    {
        if (!($person = $this->getAuthentication()->getPersonObject()))
            return new ViewModel();

        $groups = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findAllGroupsByPerson($this->getAuthentication()->getPersonObject());

        return new ViewModel(
            array(
                'groups' => $groups,
            )
        );
    }

    public function viewAction()
    {
        if (!($person = $this->getAuthentication()->getPersonObject()))
            return new ViewModel();

        if(!($group = $this->_getGroup()))

            return new ViewModel();

        $viewerMap = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndGroup($person, $group);

        if (!$viewerMap) {
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to the given form group!'
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'index'
                )
            );

            return new ViewModel();
        }

        return new ViewModel(
            array(
                'group' => $group,
                'completedEntries' => $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findCompletedByGroup($group),
                'notCompletedEntries' => $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findNotCompletedByGroup($group),
            )
        );
    }

    private function _getGroup()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the group!'
            );

            $this->redirect()->toRoute(
                'form_manage_group',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group')
            ->findOneById($this->getParam('id'));

        if (null === $group) {
            $this->flashMessenger()->error(
                'Error',
                'No group with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'form_manage_group',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        $group->setEntityManager($this->getEntityManager());

        return $group;
    }
}
