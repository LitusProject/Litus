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
    CommonBundle\Component\FlashMessenger\FlashMessage,
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
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'Please login to edit your CV.'),
                    ),
                )
            );
        }

        if (!($person instanceof Academic)) {
            return new ViewModel(
                array(
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'You must be a student to edit your CV.'),
                    ),
                )
            );
        }

        if ($this->getLanguage()->getName() == 'English') {
            $this->redirect()->toRoute(
                'br_cv_index',
                array(
                    'action' => 'cv',
                    'language' => 'nl',
                )
            );
        }

        $message = $this->_getBadAccountMessage($person);
        if ($message !== null) {
            return new ViewModel(
                array(
                    'messages' => array(
                        $message,
                    ),
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
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'The CV Book is currently not accepting entries.'),
                    ),
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
            $form->setData($formData);

            if ($form->isValid()) {
                $entry = $form->hydrateObject(
                    new CvEntry(
                        $person,
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
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'Please login to edit your CV.'),
                    ),
                )
            );
        }

        if (!($person instanceof Academic)) {
            return new ViewModel(
                array(
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'You must be a student to edit your CV.'),
                    ),
                )
            );
        }

        $message = $this->_getBadAccountMessage($person);
        if ($message !== null) {
            return new ViewModel(
                array(
                    'messages' => array(
                        $message,
                    ),
                )
            );
        }

        $entry = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findOneByAcademic($person);

        if (null === $entry) {
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
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'The CV Book is currently not accepting entries.'),
                    ),
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
        $content = '';

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($person, $this->getCurrentAcademicYear());

        if (empty($studies)) {
            $content .= '<li>' . $this->getTranslator()->translate('Your studies') . '</li>';
        }

        $address = $person->getSecondaryAddress();
        if ($address === null || '' == $address->getStreet() || '' == $address->getNumber()
                || '' == $address->getPostal() || '' == $address->getCity() || '' == $address->getCountryCode()) {
            $content .= '<li>' . $this->getTranslator()->translate('Your address') . '</li>';
        }

        if ('' == $person->getFirstName() || '' == $person->getLastName()) {
            $content .= '<li>' . $this->getTranslator()->translate('Your name') . '</li>';
        }

        if ('' == $person->getPhoneNumber()) {
            $content .= '<li>' . $this->getTranslator()->translate('Your phone number') . '</li>';
        }

        if ('' == $person->getPersonalEmail()) {
            $content .= '<li>' . $this->getTranslator()->translate('Your personal email address') . '</li>';
        }

        if ('' == $person->getPhotoPath()) {
            $content .= '<li>' . $this->getTranslator()->translate('Your photo') . '</li>';
        }

        if (null === $person->getBirthDay()) {
            $content .= '<li>' . $this->getTranslator()->translate('Your birthday') . '</li>';
        }

        if ($content != '') {
            $content = $this->getTranslator()->translate('The following information in your account is incomplete:') . '<br/><ul>' . $content . '</ul>' .
                $this->getTranslator()->translate('To add your information to the CV Book, you must complete these. Please click <a href="{{editurl}}">here</a> to edit your account.');

            return new FlashMessage('danger', 'Error', $content);
        }
    }
}
