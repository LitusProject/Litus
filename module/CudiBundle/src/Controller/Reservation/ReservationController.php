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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Reservation;

use CommonBundle\Entity\Users\People\Academic,
    Zend\View\Model\ViewModel,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Form\Reservation\Reservation as ReservationForm;

/**
 * ReservationController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ReservationController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        $authenticatedPerson = $this->getAuthentication()->getPersonObject();
        
        if (null === $authenticatedPerson) {
            return new ViewModel();
        }
        
        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($authenticatedPerson);
        
        return new ViewModel(
            array(
                'bookings' => $bookings,
            )
        );
    }
    
    public function reserveAction()
    {
        $form = new ReservationForm($this->getEntityManager());
        
        $authenticatedPerson = $this->getAuthentication()->getPersonObject();
        
        if (null === $authenticatedPerson || !($authenticatedPerson instanceof Academic)) {
            return new ViewModel();
        }
        
        $currentYear = $this->getCurrentAcademicYear();
        
        $commonArticles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findAllByTypeAndAcademicYear('common', $currentYear);
        
        $articles = array();
        foreach ($commonArticles as $commonArticle) {
            $articles[] = array(
                'article'   => $commonArticle,
                'mandatory' => false,
            );
        }
        
        $result = array();
        $result[] = array(
            'subject'   => null,
            'articles'  => $articles,
            'isMapping' => false,
        );
        
        $form->addInputsForArticles($articles);
        
        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($authenticatedPerson, $currentYear);
        
        foreach ($enrollments as $enrollment) {
            
            $subject = $enrollment->getSubject();
            
            $subjectMaps = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
                ->findAllBySubjectAndAcademicYear($subject, $currentYear);
            
            $articles = array();
            foreach ($subjectMaps as $subjectMap) {
                
                $article = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sales\Article')
                        ->findOneByArticleAndAcademicYear($subjectMap->getArticle(), $currentYear);
                
                $articles[] = array(
                    'article'   => $article,
                    'mandatory' => $subjectMap->isMandatory()
                );
            }
            
            $result[] = array(
                'subject'   => $subject,
                'articles'  => $articles,
                'isMapping' => false,
            );
            
            $form->addInputsForArticles($articles);
        }
        
        if($this->getRequest()->isPost()) {
            // Form is being posted, persist the new driver.
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
        
            if ($form->isValid()) {
                
        
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCES',
                        'The textbooks have been booked!'
                    )
                );
        
                $this->redirect()->toRoute(
                    'reservation',
                    array(
                        'action' => 'view',
                    )
                );

                return new ViewModel();
            }
        }
        
        return new ViewModel(
            array(
                'subjectArticleMap' => $result,
                'form'              => $form,
            )
        );
    }
}