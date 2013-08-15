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

namespace CudiBundle\Controller\Prof;

use CudiBundle\Entity\Article,
    Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class IndexController extends \CudiBundle\Component\Controller\ProfController
{
    public function indexAction()
    {
        $this->paginator()->setItemsPerPage(5);
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByPerson($this->getAuthentication()->getPersonObject()),
            $this->getParam('page')
        );

        foreach($paginator as $action)
            $action->setEntityManager($this->getEntityManager());

        $recentConversations = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findRecentConversationsByPersonAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'recentConversations' => $recentConversations,
            )
        );
    }
}
