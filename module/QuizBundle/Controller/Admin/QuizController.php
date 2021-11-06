<?php

namespace QuizBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use QuizBundle\Entity\Quiz;

/**
 * QuizController
 *
 * Controller for /admin/quiz[/:action[/:id]][/page/:page][/]
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class QuizController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $quizes = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Quiz')
            ->findAll();

        foreach ($quizes as $key => $quiz) {
            if (!$quiz->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
                unset($quizes[$key]);
            }
        }

        $paginator = $this->paginator()->createFromArray(
            $quizes,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('quiz_quiz_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $quiz = $form->hydrateObject();
                $this->getEntityManager()->persist($quiz);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The quiz was successfully added!'
                );

                $this->redirect()->toRoute(
                    'quiz_admin_round',
                    array(
                        'action' => 'add',
                        'quizid' => $quiz->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $quiz = $this->getQuizEntity();
        if ($quiz === null) {
            return new ViewModel();
        }

        $form = $this->getForm('quiz_quiz_edit', $quiz);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The quiz was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'quiz_admin_quiz',
                    array(
                        'action' => 'manage',
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'quiz' => $quiz,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $quiz = $this->getQuizEntity();
        if ($quiz === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($quiz);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Quiz|null
     */
    private function getQuizEntity()
    {
        $quiz = $this->getEntityById('QuizBundle\Entity\Quiz');

        if (!($quiz instanceof Quiz) || !$quiz->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'No quiz was found!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $quiz;
    }
}
