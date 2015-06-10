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

namespace SyllabusBundle\Controller\Admin\Study;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    SyllabusBundle\Entity\Study,
    Zend\View\Model\ViewModel;

/**
 * ModuleGroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ModuleGroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (null !== $this->getParam('field')) {
            $moduleGroups = $this->search();
        }

        if (!isset($studies)) {
            $moduleGroups = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
                ->findAllQuery();
        }

        $paginator = $this->paginator()->createFromQuery(
            $moduleGroups,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function typeaheadAction()
    {
        $moduleGroups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
            ->findAllByTitleTypeaheadQuery($this->getParam('string'))
            ->setMaxResults(20)
            ->getResult();

        $result = array();
        foreach ($moduleGroups as $group) {
            $item = (object) array();
            $item->id = $group->getId();
            $item->value = 'Phase ' . $group->getPhase() . '&mdash;' . $group->getTitle();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $moduleGroups = $this->search()
            ->getResult();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($moduleGroups, $numResults);

        $result = array();
        foreach ($moduleGroups as $moduleGroup) {
            $item = (object) array();
            $item->id = $moduleGroup->getId();
            $item->title = $moduleGroup->getTitle();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
                    ->findAllByTitleQuery($this->getParam('string'));
        }
    }
}
