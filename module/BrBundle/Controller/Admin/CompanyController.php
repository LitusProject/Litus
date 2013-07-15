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

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Company,
    BrBundle\Entity\Company\Page,
    BrBundle\Form\Admin\Company\Add as AddForm,
    BrBundle\Form\Admin\Company\Edit as EditForm,
    BrBundle\Form\Admin\Company\Logo as LogoForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Address,
    Imagick,
    Zend\Http\Headers,
    Zend\File\Transfer\Transfer as FileTransfer,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\View\Model\ViewModel;

/**
 * CompanyController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CompanyController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Company',
            $this->getParam('page'),
            array(
                'active' => true
            ),
            array(
                'name' => 'ASC'
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
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
                $formData = $form->getFormData($formData);

                $company = new Company(
                    $formData['company_name'],
                    $formData['vat_number'],
                    new Address(
                        $formData['address_street'],
                        $formData['address_number'],
                        $formData['address_mailbox'],
                        $formData['address_postal'],
                        $formData['address_city'],
                        $formData['address_country']
                    ),
                    $formData['website'],
                    $formData['sector']
                );

                $this->getEntityManager()->persist($company);

                $years = array();
                if (count($formData['cvbook']) > 0) {
                    $yearIds = $formData['cvbook'];
                    $repository = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\AcademicYear');
                    foreach($yearIds as $yearId) {
                        $years[] = $repository->findOneById($yearId);
                    }
                }

                $company->setCvBookYears($years);

                $years = array();
                if (count($formData['years']) > 0) {
                    $yearIds = $formData['years'];
                    $repository = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\AcademicYear');
                    foreach($yearIds as $yearId) {
                        $years[] = $repository->findOneById($yearId);
                    }
                }

                $page = new Page(
                    $company,
                    $formData['summary'],
                    $formData['description']
                );

                $page->setYears($years);

                $this->getEntityManager()->persist($page);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The company was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_company',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($company = $this->_getCompany()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $company);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $company->setName($formData['company_name'])
                    ->setVatNumber($formData['vat_number'])
                    ->setSector($formData['sector'])
                    ->setWebsite($formData['website'])
                    ->getAddress()
                        ->setStreet($formData['address_street'])
                        ->setNumber($formData['address_number'])
                        ->setMailbox($formData['address_mailbox'])
                        ->setPostal($formData['address_postal'])
                        ->setCity($formData['address_city'])
                        ->setCountry($formData['address_country']);

                $years = array();
                if (count($formData['cvbook']) > 0) {
                    $yearIds = $formData['cvbook'];
                    $repository = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\AcademicYear');
                    foreach($yearIds as $yearId) {
                        $years[] = $repository->findOneById($yearId);
                    }
                }

                $company->setCvBookYears($years);

                $years = array();
                if (count($formData['years']) > 0) {
                    $yearIds = $formData['years'];
                    $repository = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\AcademicYear');
                    foreach($yearIds as $yearId) {
                        $years[] = $repository->findOneById($yearId);
                    }
                }

                $company->getPage()
                    ->setSummary($formData['summary'])
                    ->setDescription($formData['description'])
                    ->setYears($years);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The company was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_company',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'company' => $company,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($company = $this->_getCompany()))
            return new ViewModel();

        $company->deactivate();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function uploadAction()
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (!(in_array($_FILES['file']['type'], array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif')) && $_POST['type'] == 'image') &&
                    $_POST['type'] !== 'file') {
                return new ViewModel();
            }

            $filePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/';

            $upload = new FileUpload();

            $fileName = '';
            do{
                $fileName = sha1(uniqid());
            } while (file_exists($filePath . $fileName));

            $upload->addFilter('Rename', $filePath . $fileName);
            $upload->receive();

            $url = $this->url()->fromRoute(
                'br_career_file',
                array(
                    'name' => $fileName,
                )
            );

            return new ViewModel(
                array(
                    'result' => array(
                        'name' => $url,
                    )
                )
            );
        }
        return new ViewModel();
    }

    public function editLogoAction()
    {
        if (!($company = $this->_getCompany()))
            return new ViewModel();

        $form = new LogoForm();

        $filePath = 'public/' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $file = new FileTransfer();
                $file->receive();

                $image = new Imagick($file->getFileName());
                unlink($file->getFileName());
                $image->thumbnailImage(320, 320, true);

                if ($company->getLogo() != '' || $company->getLogo() !== null) {
                    $fileName = '/' . $company->getLogo();
                } else {
                    $fileName = '';
                    do{
                        $fileName = '/' . sha1(uniqid());
                    } while (file_exists($filePath . $fileName));
                }
                $image->writeImage($filePath . $fileName);
                $company->setLogo($fileName);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The company\'s logo has successfully been updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_company',
                    array(
                        'action' => 'editLogo',
                        'id' => $company->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $company,
                'form' => $form,
                'logoPath' => $filePath,
            )
        );
    }

    private function _getCompany()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the company!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No company with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $company;
    }

    private function _getCompanyByLogo()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the company!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneByLogo($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No company with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $company;
    }
}
