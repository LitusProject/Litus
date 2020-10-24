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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Controller\Manage;

use FormBundle\Entity\Node\Group;
use Laminas\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \FormBundle\Component\Controller\FormController
{
    public function indexAction()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $groups = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findAllGroupsByPerson($person);

        return new ViewModel(
            array(
                'groups' => $groups,
            )
        );
    }

    public function viewAction()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $group = $this->getGroupEntity();
        if ($group === null) {
            return new ViewModel();
        }

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
                    'action' => 'index',
                )
            );

            return new ViewModel();
        }

        return new ViewModel(
            array(
                'group'            => $group,
                'completedEntries' => $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findCompletedByGroup($group),
                'notCompletedEntries' => $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findNotCompletedByGroup($group),
            )
        );
    }

    /**
     * @return Group|null
     */
    private function getGroupEntity()
    {
        $group = $this->getEntityById('FormBundle\Entity\Node\Group');

        if (!($group instanceof Group)) {
            $this->flashMessenger()->error(
                'Error',
                'No group was found!'
            );

            $this->redirect()->toRoute(
                'form_manage_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $group->setEntityManager($this->getEntityManager());

        return $group;
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        return $this->getAuthentication()->getPersonObject();
    }
}
