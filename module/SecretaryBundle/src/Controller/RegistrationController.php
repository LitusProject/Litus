<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Address,
    DateTime,
    SecretaryBundle\Form\Registration\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * RegistrationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function addAction()
    {
        $enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.registration_enabled');

        if ('1' !== $enabled)
            return $this->notFoundAction();

        $student = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneByUniversityIdentification($this->getParam('identification'));

        if (false && null !== $student) { // TODO: remove false
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'There is already a user with your university identification!'
                )
            );

            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add'
                )
            );

            return new ViewModel(
                array(
                    'registerShibbolethUrl' => $this->_getRegisterhibbolethUrl(),
                )
            );
        }

        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        if ($this->getRequest()->isPost()) {
            if (true || $code->hash() == $this->getParam('hash')) { // TODO: remove true
                $form = new AddForm($this->getCache(), $this->getEntityManager(), $this->getParam('identification'));

                $formData = $this->getRequest()->getPost();
                $formData['university_identification'] = $this->getParam('identification');
                $form->setData($formData);

                if ($form->isValid()) {
                    $roles = array(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('guest'),
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('student')
                    );

                    $student = new Academic(
                        $this->getParam('identification'),
                        $roles,
                        $formData['first_name'],
                        $formData['last_name'],
                        $formData['primary_email'] ? $formData['personal_email'] : $formData['university_email'],
                        $formData['phone_number'],
                        $formData['sex'],
                        $this->getParam('identification')
                    );

                    $primaryCity = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Address\City')
                        ->findOneById($formData['primary_address_address_city']);
                    $primaryStreet = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Address\City')
                        ->findOneById($formData['primary_address_address_street']);

                    $student->setBirthday(DateTime::createFromFormat('d/m/Y H:i', $formData['birthday'] . ' 00:00'))
                        ->addUniversityStatus(
                            new UniversityStatus(
                                $student,
                                'student',
                                $this->getCurrentAcademicYear()
                            )
                        )
                        ->setPersonalEmail($formData['personal_email'])
                        ->setUniversityEmail($formData['university_email'])
                        ->setPrimaryAddress(
                            new Address(
                                $primaryStreet,
                                $formData['primary_address_address_number'],
                                $primaryCity->getPostal(),
                                $primaryCity->getName(),
                                'BE'
                            )
                        )
                        ->setSecondaryAddress(
                            new Address(
                                $formData['secondary_address_address_street'],
                                $formData['secondary_address_address_number'],
                                $formData['secondary_address_address_postal'],
                                $formData['secondary_address_address_city'],
                                $formData['secondary_address_address_country']
                            )
                        );

                    $filePath = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('common.profile_path');

                    $file = new FileTransfer();
                    if ($file->receive()) {
                        $image = new Imagick($file->getFileName());
                        $image->cropThumbnailImage(320, 240);

                        if ($student->getPhotoPath() != '' || $student->getPhotoPath() !== null) {
                            $fileName = $student->getPhotoPath();
                        } else {
                            $fileName = '';
                            do{
                                $fileName = '/' . sha1(uniqid());
                            } while (file_exists($filePath . $fileName));
                        }
                        $image->writeImage($filePath . $fileName);
                        $student->setPhotoPath($fileName);
                    }

                    $student->activate(
                        $this->getEntityManager(),
                        $this->getMailTransport()
                    );

                    $this->getEntityManager()->persist($student);
                    $this->getEntityManager()->flush();
                }

                return new ViewModel(
                    array(
                        'form' => $form,
                    )
                );
            }
        } else {
            if (null !== $code || true) { // TODO: remove true
                if (true || $code->validate($this->getParam('hash'))) { // TODO: remove true
                    $form = new AddForm($this->getCache(), $this->getEntityManager(), $this->getParam('identification'));

                    return new ViewModel(
                        array(
                            'form' => $form,
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'registerShibbolethUrl' => $this->_getRegisterhibbolethUrl(),
            )
        );
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     */
    private function _getRegisterhibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if ('%2F' != substr($shibbolethUrl, 0, -3))
            $shibbolethUrl .= '%2F';

        return $shibbolethUrl . '?source=register';
    }
}
