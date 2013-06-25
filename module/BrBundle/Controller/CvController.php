<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace BrBundle\Controller;

use BrBundle\Entity\Cv\Entry as CvEntry,
    BrBundle\Entity\Cv\Language as CvLanguage,
    BrBundle\Form\Cv\Add as AddForm,
    BrBundle\Form\Cv\Edit as EditForm,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\Users\People\Academic,
    CommonBundle\Component\FlashMessenger\FlashMessage,
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
        $messages = array();
        $languageError = null;

        if ($person === null) {
            $messages = array('Please login to add your CV.');
        } else {
            if (!($person instanceof Academic)) {
                $messages = array('You must be a student to add your CV.');
            } else {

                $temp = $this->_getBadAccountMessage($person);
                if ($temp !== null && !empty($temp))
                    $messages = $temp;

                $entry = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Cv\Entry')
                    ->findOneByAcademic($person);
                if ($entry) {
                    $messages = array('');
                    $this->redirect()->toRoute(
                        'br_cv_index',
                        array(
                            'action' => 'edit',
                        )
                    );
                }
            }
        }


        $open = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_open');

        if (!$open) {
            $messages = array('The CV Book is currently not accepting entries.');
        }

        if (!empty($messages)) {
            return new ViewModel(
                array(
                    'messages' => $messages,
                )
            );
        }

        $form = new AddForm($this->getEntityManager(), $person, $this->getCurrentAcademicYear(), $this->getLanguage());

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();
            $formData = $form->addLanguages($formData);
            $form->setData($formData);

            if ($form->isValid()) {

                $address = new Address(
                    $person->getSecondaryAddress()->getStreet(),
                    $person->getSecondaryAddress()->getNumber(),
                    $person->getSecondaryAddress()->getMailbox(),
                    $person->getSecondaryAddress()->getPostal(),
                    $person->getSecondaryAddress()->getCity(),
                    $person->getSecondaryAddress()->getCountryCode()
                );

                $entry = new CvEntry(
                    $person,
                    $this->getCurrentAcademicYear(),
                    $person->getFirstName(),
                    $person->getLastName(),
                    $person->getBirthDay(),
                    $person->getSex(),
                    $person->getPhoneNumber(),
                    $person->getPersonalEmail(),
                    $address,
                    $formData['prior_degree'],
                    $formData['prior_grade'],
                    $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Study')
                        ->findOneById($formData['degree']),
                    $formData['grade'],
                    $formData['bachelor_start'],
                    $formData['bachelor_end'],
                    $formData['master_start'],
                    $formData['master_end'],
                    $formData['additional_diplomas'],
                    $formData['erasmus_period'],
                    $formData['erasmus_location'],
                    $formData['lang_extra'],
                    $formData['computer_skills'],
                    $formData['experiences'],
                    $formData['thesis_summary'],
                    $formData['field_of_interest'],
                    $formData['mobility_europe'],
                    $formData['mobility_world'],
                    $formData['career_expectations'],
                    $formData['hobbies'],
                    $formData['profile_about']
                );

                for ($i = 0; $i < $formData['lang_count']; $i++) {
                    if (!isset($formData['lang_name' . $i]) || '' === $formData['lang_name' . $i])
                        continue;

                    $language = new CvLanguage($entry, $formData['lang_name' . $i],
                        $formData['lang_written' . $i],
                        $formData['lang_oral' . $i]);

                    $this->getEntityManager()->persist($language);
                }

                $this->getEntityManager()->persist($entry);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'br_cv_index',
                    array(
                        'action' => 'complete',
                    )
                );

                return new ViewModel();
            } else {
                if (!$form->isValidLanguages($formData)) {
                    $languageError = 'The number of languages must be between 1 and 5';
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'languageError' => $languageError,
                'oral_skills' => CvLanguage::$ORAL_SKILLS,
                'written_skills' => CvLanguage::$WRITTEN_SKILLS,
            )
        );
    }

    public function editAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        $messages = array();
        $languageError = null;

        if ($person === null) {
            $messages = array('Please login to edit your CV.');
        } else {
            if (!($person instanceof Academic)) {
                $messages = array('You must be a student to edit your CV.');
            } else {

                $temp = $this->_getBadAccountMessage($person);
                if ($temp !== null && !empty($temp))
                    $messages = $temp;

                $entry = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Cv\Entry')
                    ->findOneByAcademic($person);
                if (!$entry) {
                    $messages = array('');
                    $this->redirect()->toRoute(
                        'br_cv_index',
                        array(
                            'action' => 'cv',
                        )
                    );
                }
            }
        }

        $open = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_open');

        if (!$open) {
            $messages = array('The CV Book is currently not accepting entries.');
        }

        if (!empty($messages)) {
            return new ViewModel(
                array(
                    'messages' => $messages,
                )
            );
        }

        $form = new EditForm($this->getEntityManager(), $person, $this->getCurrentAcademicYear(), $this->getLanguage());

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();
            $formData = $form->addLanguages($formData);
            $form->setData($formData);

            if ($form->isValid()) {

                $address = new Address(
                    $person->getSecondaryAddress()->getStreet(),
                    $person->getSecondaryAddress()->getNumber(),
                    $person->getSecondaryAddress()->getMailbox(),
                    $person->getSecondaryAddress()->getPostal(),
                    $person->getSecondaryAddress()->getCity(),
                    $person->getSecondaryAddress()->getCountryCode()
                );

                $entry
                    ->setFirstName($person->getFirstName())
                    ->setLastName($person->getLastName())
                    ->setBirthday($person->getBirthDay())
                    ->setSex($person->getSex())
                    ->setPhoneNumber($person->getPhoneNumber())
                    ->setEmail($person->getPersonalEmail())
                    ->setAddress($address)
                    ->setPriorStudy($formData['prior_degree'])
                    ->setPriorGrade($formData['prior_grade'] * 100)
                    ->setStudy($this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Study')
                        ->findOneById($formData['degree']))
                    ->setGrade($formData['grade'] * 100)
                    ->setBachelorStart($formData['bachelor_start'])
                    ->setBachelorEnd($formData['bachelor_end'])
                    ->setMasterStart($formData['master_start'])
                    ->setMasterEnd($formData['master_end'])
                    ->setAdditionalDiplomas($formData['additional_diplomas'])
                    ->setErasmusPeriod($formData['erasmus_period'])
                    ->setErasmusLocation($formData['erasmus_location'])
                    ->setLanguageExtra($formData['lang_extra'])
                    ->setComputerSkills($formData['computer_skills'])
                    ->setExperiences($formData['experiences'])
                    ->setThesisSummary($formData['thesis_summary'])
                    ->setFutureInterest($formData['field_of_interest'])
                    ->setMobilityEurope($formData['mobility_europe'])
                    ->setMobilityWorld($formData['mobility_world'])
                    ->setCareerExpectations($formData['career_expectations'])
                    ->setHobbies($formData['hobbies'])
                    ->setAbout($formData['profile_about']);

                // Clear all previous languages
                $languages = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Cv\Language')
                    ->findByEntry($entry);

                foreach($languages as $language) {
                    $this->getEntityManager()->remove($language);
                }

                for ($i = 0; $i < $formData['lang_count']; $i++) {
                    if (!isset($formData['lang_name' . $i]) || '' === $formData['lang_name' . $i])
                        continue;

                    $language = new CvLanguage($entry, $formData['lang_name' . $i],
                        $formData['lang_written' . $i],
                        $formData['lang_oral' . $i]);

                    $this->getEntityManager()->persist($language);
                }

                $this->getEntityManager()->persist($entry);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'br_cv_index',
                    array(
                        'action' => 'complete',
                    )
                );

                return new ViewModel();
            } else {
                if (!$form->isValidLanguages($formData)) {
                    $languageError = 'The number of languages must be between 1 and 5';
                }
            }
        } else {
            $form->populateFromEntry($entry);
        }

        return new ViewModel(
            array(
                'form' => $form,
                'languageError' => $languageError,
                'oral_skills' => CvLanguage::$ORAL_SKILLS,
                'written_skills' => CvLanguage::$WRITTEN_SKILLS,
            )
        );
    }


    public function completeAction()
    {
        return new ViewModel();
    }

    private function _getBadAccountMessage(Academic $person) {
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
