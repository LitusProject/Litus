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

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Component\Mail\Booking as BookingMail,
    CudiBundle\Entity\Sale\Booking,
    CudiBundle\Form\Admin\SpecialActions\Irreeel\Assign as IrreeelForm,
    Zend\View\Model\ViewModel;

/**
 * SpecialActionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SpecialActionController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        return new ViewModel();
    }

    public function irreeelAction()
    {
        $form = new IrreeelForm();

        $academicYear = $this->getCurrentAcademicYear();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $number = 0;
                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article_id']);

                $criteria = array('academicYear' => $academicYear->getId());

                if ($formData['only_cudi'])
                    $criteria['irreeelAtCudi'] = true;

                $people = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
                    ->findBy($criteria);

                foreach ($people as $person) {
                    $registration = $this->getEntityManager()
                        ->getRepository('SecretaryBundle\Entity\Registration')
                        ->findOneByAcademic($person->getAcademic());
                    if (null === $registration)
                        continue;

                    if ($person->getAcademic()->isMember($academicYear) && $registration->hasPayed()) {
                        $booking = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Booking')
                            ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                                $article,
                                $person->getAcademic(),
                                $academicYear
                            );

                        if (null === $booking) {
                            $number++;
                            $booking = new Booking($this->getEntityManager(), $person->getAcademic(), $article, 'assigned', 1, true);
                            $this->getEntityManager()->persist($booking);

                            if (!$formData['test'] && $formData['send_mail'])
                                BookingMail::sendAssignMail($this->getEntityManager(), $this->getMailTransport(), array($booking), $booking->getPerson());
                        } elseif ($booking->getStatus() == 'booked') {
                            $number++;
                            $booking->setStatus('assigned', $this->getEntityManager());
                        }
                    }
                }

                if (!$formData['test']) {
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'There are <b>' . $number . '</b> ' . $article->getMainArticle()->getTitle() . ' assigned!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'cudi_admin_special_action',
                        array(
                            'action' => 'manage',
                        )
                    );

                    return new ViewModel(
                        array(
                            'currentAcademicYear' => $academicYear,
                        )
                    );
                } else {
                    $this->getEntityManager()->clear();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'There are <b>' . $number . '</b> ' . $article->getMainArticle()->getTitle() . ' would be assigned!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'cudi_admin_special_action',
                        array(
                            'action' => 'irreeel',
                        )
                    );

                    return new ViewModel(
                        array(
                            'currentAcademicYear' => $academicYear,
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'currentAcademicYear' => $academicYear,
            )
        );
    }
}
