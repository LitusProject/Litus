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

namespace BrBundle\Controller\Career;

use Laminas\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class IndexController extends \BrBundle\Component\Controller\CareerController
{
    public function indexAction()
    {
        $this->redirect()->toRoute(
            'br_career_company',
            array(
                'action' => 'overview',
            )
        );

        return new ViewModel();
    }

    public function calendarAction(){
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllCareerAndActiveAndNotHidden();

        $calendarItems = array();
        foreach ($events as $event) {
            $calendarItems[$event->getId()] = $event;
        }

        return new ViewModel(
            array(
                'entityManager' => $this->getEntityManager(),
                'calendarItems' => $calendarItems,
            )
        );
    }
}
