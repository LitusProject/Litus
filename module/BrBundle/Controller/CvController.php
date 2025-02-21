<?php

namespace BrBundle\Controller;

use BrBundle\Component\Document\Generator\Pdf\Cv as CvGenerator;
use BrBundle\Entity\Cv\Entry as CvEntry;
use CommonBundle\Component\FlashMessenger\FlashMessage;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Entity\User\Person\Academic;
use Imagick;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * CvController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function cvAction()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return new ViewModel(
                array(
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'Please login to edit your CV.'),
                    ),
                )
            );
        }

        $person = $this->getAuthentication()->getPersonObject();

        if (!($person instanceof Academic)) {
            return new ViewModel(
                array(
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'You must be a student to edit your CV.'),
                    ),
                )
            );
        }

        $academicRepository = $this->getEntityManager()->getRepository('CommonBundle\Entity\User\Person\Academic');
        $isLastYear = $academicRepository->isLastYear($person->getId());

        if (!$isLastYear) {
            return new ViewModel(
                array(
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'You must be a last year student to edit your CV.'),
                    ),
                )
            );
        }


        $message = $this->getBadAccountMessage($person);
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
            ->findOneByAcademicAndAcademicYear($this->getCurrentAcademicYear(), $person);

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
                'academic'     => $person,
                'academicYear' => $this->getCurrentAcademicYear(),
            )
        );


        $profileForm = $this->getForm('br_cv_profile');
        $profileForm->setAttribute(
            'action',
            $this->url()->fromRoute(
                'br_cv_index',
                array(
                    'action' => 'uploadProfileImage',
                )
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
                'form'        => $form,
                'profilePath' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'profileForm' => $profileForm,
            )
        );
    }

    public function editAction()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return new ViewModel(
                array(
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'Please login to edit your CV.'),
                    ),
                )
            );
        }

        $person = $this->getAuthentication()->getPersonObject();

        if (!($person instanceof Academic)) {
            return new ViewModel(
                array(
                    'messages' => array(
                        new FlashMessage('danger', 'Error', 'You must be a student to edit your CV.'),
                    ),
                )
            );
        }

        $message = $this->getBadAccountMessage($person);
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
            ->findOneByAcademicAndAcademicYear($this->getCurrentAcademicYear(), $person);

        if ($entry === null) {
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
                'academic'     => $person,
                'academicYear' => $this->getCurrentAcademicYear(),
                'entry'        => $entry,
            )
        );

        $profileForm = $this->getForm('br_cv_profile');
        $profileForm->setAttribute(
            'action',
            $this->url()->fromRoute(
                'br_cv_index',
                array(
                    'action' => 'uploadProfileImage',
                )
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
                'form'        => $form,
                'profilePath' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'profileForm' => $profileForm,
            )
        );
    }

    public function completeAction()
    {
        return new ViewModel();
    }

    public function downloadAction()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->redirect()->toRoute(
                'br_cv_index',
                array(
                    'action' => 'cv',
                )
            );

            return new ViewModel();
        }

        $person = $this->getAuthentication()->getPersonObject();

        $entry = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findOneByAcademicAndAcademicYear($this->getCurrentAcademicYear(), $person);

        if ($entry === null) {
            $this->redirect()->toRoute(
                'br_cv_index',
                array(
                    'action' => 'cv',
                )
            );

            return new ViewModel();
        }

        $file = new TmpFile();

        $translator = $this->getTranslator();
        $document = new CvGenerator($this->getEntityManager(), $entry, $file, $translator);

        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="cv-' . $person->getFullName() . '.pdf"',
                'Content-type'        => 'application/pdf',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    /**
     * @param  Academic $person
     * @return FlashMessage|null
     */
    private function getBadAccountMessage(Academic $person)
    {
        $content = '';

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($person, $this->getCurrentAcademicYear());

        if (count($studies) == 0) {
            $content .= '<li>' . $this->getTranslator()->translate('Your studies') . '</li>';
        }

        $address = $person->getSecondaryAddress();
        if ($address === null || $address->getStreet() == '' || $address->getNumber() == ''
            || $address->getPostal() == '' || $address->getCity() == '' || $address->getCountryCode() == ''
        ) {
            $content .= '<li>' . $this->getTranslator()->translate('Your address') . '</li>';
        }

        if ($person->getFirstName() == '' || $person->getLastName() == '') {
            $content .= '<li>' . $this->getTranslator()->translate('Your name') . '</li>';
        }

        if ($person->getPhoneNumber() == '') {
            $content .= '<li>' . $this->getTranslator()->translate('Your phone number') . '</li>';
        }

        if ($person->getPersonalEmail() == '') {
            $content .= '<li>' . $this->getTranslator()->translate('Your personal email address') . '</li>';
        }

        if ($person->getBirthDay() === null) {
            $content .= '<li>' . $this->getTranslator()->translate('Your birthday') . '</li>';
        }

        if ($content != '') {
            $content = $this->getTranslator()->translate('The following information in your account is incomplete:') . '<br/><ul>' . $content . '</ul>' .
                $this->getTranslator()->translate('To add your information to the CV Book, you must complete these. Please click <a href="{{editurl}}">here</a> to edit your account.');

            return new FlashMessage('danger', 'Error', $content);
        }
    }

    public function uploadProfileImageAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_cv_profile');

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            $filePath = 'public' . $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.profile_path');

            if ($form->isValid()) {
                $formData = $form->getData();

                if ($formData['profile']) {
                    $image = new Imagick($formData['profile']['tmp_name']);
                } else {
                    $image = new Imagick($filePath . '/' . $academic->getPhotoPath());
                }

                if ($formData['x'] == 0 && $formData['y'] == 0 && $formData['x2'] == 0 && $formData['y2'] == 0 && $formData['w'] == 0 && $formData['h'] == 0) {
                    $image->cropThumbnailImage(320, 240);
                } else {
                    $ratio = $image->getImageWidth() / 320;
                    $x = $formData['x'] * $ratio;
                    $y = $formData['y'] * $ratio;
                    $w = $formData['w'] * $ratio;
                    $h = $formData['h'] * $ratio;

                    $image->cropImage($w, $h, $x, $y);
                    $image->cropThumbnailImage(320, 240);
                }

                do {
                    $newFileName = sha1(uniqid());
                } while (file_exists($filePath . '/' . $newFileName));

                if ($academic->getPhotoPath() != '' || $academic->getPhotoPath() !== null) {
                    $fileName = $academic->getPhotoPath();

                    if (file_exists($filePath . '/' . $fileName)) {
                        unlink($filePath . '/' . $fileName);
                    }
                }

                $image->writeImage($filePath . '/' . $newFileName);
                $academic->setPhotoPath($newFileName);

                $this->getEntityManager()->flush();

                return new ViewModel(
                    array(
                        'result' => array(
                            'status'  => 'success',
                            'profile' => $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('common.profile_path') . '/' . $newFileName,
                        ),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'error',
                            'form'   => array(
                                'errors' => $form->getMessages(),
                            ),
                        ),
                    )
                );
            }
        }
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return;
        }

        return $academic;
    }

    public function deleteAction()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->redirect()->toRoute(
                'br_cv_index',
                array(
                    'action' => 'cv',
                )
            );

            return new ViewModel();
        }

        $person = $this->getAuthentication()->getPersonObject();

        $entry = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findOneByAcademicAndAcademicYear($this->getCurrentAcademicYear(), $person);

        if ($entry === null) {
            $this->redirect()->toRoute(
                'br_cv_index',
                array(
                    'action' => 'cv',
                )
            );

            return new ViewModel();
        }

        $this->getEntityManager()->remove($entry);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success!',
            'Your curriculum vitae has been removed.'
        );

        $this->redirect()->toRoute(
            'br_cv_index',
            array(
                'action' => 'cv',
            )
        );

        return new ViewModel();
    }
}
