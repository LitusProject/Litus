<?php

namespace CudiBundle\Controller\Prof;

use Laminas\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class IndexController extends \CudiBundle\Component\Controller\ProfController
{
    public function indexAction()
    {
        if ($this->getAuthentication()->isAuthenticated()) {
            $this->paginator()->setItemsPerPage(5);
            $paginator = $this->paginator()->createFromQuery(
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Prof\Action')
                    ->findAllByPersonQuery($this->getAuthentication()->getPersonObject()),
                $this->getParam('page')
            );

            foreach ($paginator as $action) {
                $action->setEntityManager($this->getEntityManager());
            }

            $recentConversations = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\Comment')
                ->findRecentConversationsByPersonAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

            return new ViewModel(
                array(
                    'paginator'           => $paginator,
                    'paginationControl'   => $this->paginator()->createControl(),
                    'recentConversations' => $recentConversations,
                )
            );
        }

        return new ViewModel();
    }
}
