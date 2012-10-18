<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace BrBundle\Controller\Career;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * VacancyController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class VacancyController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByType('vacancy'),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function viewAction()
    {
        $vacancy = $this->_getVacancy();

        return new ViewModel(
            array(
                'vacancy' => $vacancy,
            )
        );
    }

    private function _getVacancy()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the vacancy!'
                )
            );

            $this->redirect()->toRoute(
                'career_vacancy',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $vacancy = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('vacancy', $this->getParam('id'));

        if (null === $vacancy) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No vacancy with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'career_vacancy',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $vacancy;
    }
}
