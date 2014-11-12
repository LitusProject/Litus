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

namespace BrBundle\Controller;

use BrBundle\Entity\Cv\Entry as CvEntry,
    CommonBundle\Entity\User\Person\Academic,
    Zend\View\Model\ViewModel;

/**
 * CvController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function cvAction()
    {
        $person = $this->getAuthentication()->getPersonObject();

        if (null === $person) {
            return new ViewModel(
                array(
                    'messages' => array('Please login to edit your CV.'),
                )
            );
        }

        if (!($person instanceof Academic)) {
            return new ViewModel(
                array(
                    'messages' => array('You must be a student to edit your CV.'),
                )
            );
        }

        if ($this->getLanguage()->getName() == "English") {
            $this->redirect()->toRoute(
                'br_cv_index',
                array(
                    'action' => 'cv',
                    'language' => 'nl',
                )
            );
        }

        $messages = $this->_getBadAccountMessage($person);
        if ($messages !== null && !empty($messages)) {
            return new ViewModel(
                    array(
                        'messages' => $messages,
                    )
                );
        }

        $entry = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findOneByAcademic($person);

        if ($entry !== null) {
            $this->redirect()->toRoute(
                'br_cv_index',
                array(
                    'action' => 'edit',
                )
            );

            return new ViewModel();
        }

        $open = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_open');

        if (!$open) {
            return new ViewModel(
                array(
                    'messages' => array('The CV Book is currently not accepting entries.'),
                )
            );
        }

        $form = $this->getForm(
            'br_cv_add',
            array(
                'academic' => $person,
                'academicYear' => $this->getCurrentAcademicYear(),
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $formData = $form->addLanguages($formData);
            $form->setData($formData);

            if ($form->isValid()) {
                $entry = $form->hydrateObject(
                    new CvEntry(
                        $pesron,
                        $this->getCurrentAcademicYear()
                    )
                );

                $this->getEntityManager()->persist($entry);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'br_cv_index',
                    array(
                        'action' => 'complete',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'profilePath' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
            )
        );
    }

    public function editAction()
    {
        $person = $this->getAuthentication()->getPersonObject();

        if (null === $person) {
            return new ViewModel(
                array(
                    'messages' => array('Please login to edit your CV.'),
                )
            );
        }

        if (!($person instanceof Academic)) {
            return new ViewModel(
                array(
                    'messages' => array('You must be a student to edit your CV.'),
                )
            );
        }

        $messages = $this->_getBadAccountMessage($person);
        if ($messages !== null && !empty($messages)) {
            return new ViewModel(
                array(
                    'messages' => $messages,
                )
            );
        }

        $entry = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findOneByAcademic($person);

        if (!$entry) {
            $this->redirect()->toRoute(
                'br_cv_index',
                array(
                    'action' => 'cv',
                )
            );

            return new ViewModel();
        }

        $open = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_open');

        if (!$open) {
            return new ViewModel(
                array(
                    'messages' => array('The CV Book is currently not accepting entries.'),
                )
            );
        }

        $form = $this->getForm(
            'br_cv_edit',
            array(
                'academic' => $person,
                'academicYear' => $this->getCurrentAcademicYear(),
                'entry' => $entry,
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'br_cv_index',
                    array(
                        'action' => 'complete',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'profilePath' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
            )
        );
    }

    public function completeAction()
    {
        return new ViewModel();
    }

    private function _getBadAccountMessage(Academic $person)
    {
        $messages = array();

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($person, $this->getCurrentAcademicYear());

        if (empty($studies)) {
            $messages[] = '<li>';
            $messages[] = 'Your studies';
            $messages[] = '</li>';
        }

        $address = $person->getSecondaryAddress();
        if ($address === null || '' == $address->getStreet() || '' == $address->getNumber()
                || '' == $address->getPostal() || '' == $address->getCity() || '' == $address->getCountryCode()) {
            $messages[] = '<li>';
            $messages[] = 'Your address';
            $messages[] = '</li>';
        }

        if ('' == $person->getFirstName() || '' == $person->getLastName()) {
            $messages[] = '<li>';
            $messages[] = 'Your name';
            $messages[] = '</li>';
        }

        if ('' == $person->getPhoneNumber()) {
            $messages[] = '<li>';
            $messages[] = 'Your phone number';
            $messages[] = '</li>';
        }

        if ('' == $person->getPersonalEmail()) {
            $messages[] = '<li>';
            $messages[] = 'Your personal email address';
            $messages[] = '</li>';
        }

        if ('' == $person->getPhotoPath()) {
            $messages[] = '<li>';
            $messages[] = 'Your photo';
            $messages[] = '</li>';
        }

        if (null === $person->getBirthDay()) {
            $messages[] = '<li>';
            $messages[] = 'Your birthday';
            $messages[] = '</li>';
        }

        if ($messages) {
            array_unshift($messages, 'The following information in your account is incomplete:', '<br/><ul>');
            $messages[] = '</ul>';
            $messages[] = 'To add your information to the CV Book, you must complete these. Please click <a href="{{editurl}}">here</a> to edit your account.';
        }

        return $messages;
    }
}
