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

namespace QuizBundle\Controller\Admin;

use QuizBundle\Entity\Round;
use Zend\View\Model\ViewModel;

/**
 * RoundController
 *
 * Controller for /admin/quiz/:quizid/round[/:action[/:id]][/page/:page][/]
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class RoundController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($quiz = $this->_getQuiz()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromEntity(
            'QuizBundle\Entity\Round',
            $this->getParam('page'),
            array(
                'quiz' => $quiz,
            ),
            array(
                'order' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        if (!($quiz = $this->_getQuiz()))
            return new ViewModel();

        $form = $this->getForm('quiz_round_add', array('quiz' => $quiz));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $round = new Round($quiz);
                $form->hydrateObject($round);

                $this->getEntityManager()->persist($round);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The round was successfully added!'
                );

                $this->redirect()->toRoute(
                    'quiz_admin_round',
                    array(
                        'action' => 'manage',
                        'quizid' => $quiz->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $next_round_number = $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Round')
                ->getNextRoundOrderForQuiz($quiz);

        $form->get('order')
            ->setValue($next_round_number);

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($round = $this->_getRound()))
            return new ViewModel();

        $form = $this->getForm('quiz_round_edit', array('round' => $round));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The round was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'quiz_admin_round',
                    array(
                        'action' => 'manage',
                        'quizid' => $round->getQuiz()->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'quiz' => $round->getQuiz(),
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($round = $this->_getRound()))
            return new ViewModel();

        $this->getEntityManager()->remove($round);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function sortAction()
    {
        $this->initAjax();
        if(!($quiz = $this->_getQuiz()))

            return new ViewModel();

        if(!$this->getRequest()->isPost())

            return new ViewModel();

        $data = $this->getRequest()->getPost();

        if(!$data['items'])

            return new ViewModel();

        foreach ($data['items'] as $order => $id) {
            $round = $this->getEntityManager()->find('QuizBundle\Entity\Round', $id);
            $round->setOrder($order+1);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(array(
            'result' => array(
                'status' => 'success',
            ),
        ));
    }

    /**
     * @return null|\QuizBundle\Entity\Quiz
     */
    private function _getQuiz()
    {
        if ($this->getParam('quizid') === null) {
            $this->flashMessenger()->error(
                'Error',
                'No id was given to identify the quiz!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $quiz = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Quiz')
            ->findOneById($this->getParam('quizid'));

        if ($quiz === null) {
            $this->flashMessenger()->error(
                'Error',
                'No quiz with the given id was found!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        if (!$quiz->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You do not have the permissions to modify this quiz!'
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

    /**
     * @return null|Round
     */
    private function _getRound()
    {
        if ($this->getParam('id') === null) {
            $this->flashMessenger()->error(
                'Error',
                'No id was given to identify the round!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_round',
                array(
                    'action' => 'manage',
                    'quizid' => $this->getParam('quizid'),
                )
            );

            return;
        }

        $round = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Round')
            ->findOneById($this->getParam('id'));

        if ($round === null) {
            $this->flashMessenger()->error(
                'Error',
                'No round with the given id was found!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_round',
                array(
                    'action' => 'manage',
                    'quizid' => $this->getParam('quizid'),
                )
            );

            return;
        }

        if (!$round->getQuiz()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You do not have the permissions to modify this quiz!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $round;
    }
}
