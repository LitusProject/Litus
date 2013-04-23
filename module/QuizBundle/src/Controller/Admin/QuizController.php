<?php

namespace QuizBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    QuizBundle\Entity\Quiz,
    QuizBundle\Form\Admin\Quiz\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * QuizController
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class QuizController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Quiz')
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData($formData);

                // XXX: Edit role: In form or edit later?
                $quiz = new Quiz($this->getAuthentication()->getPersonObject(), $formData['name'], array());
                $this->getEntityManager()->persist($quiz);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The quiz was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'quiz_admin_quiz',
                    array(
                        'action' => 'addRounds',
                        'id' => $quiz->getId(),
                    )
                );

                return new ViewModel();
            }

            return new ViewModel(
                array(
                    'form' => $form,
                )
            );
        }
    }
}
