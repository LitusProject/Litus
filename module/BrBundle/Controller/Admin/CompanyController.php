<?php

namespace BrBundle\Controller\Admin;

use BrBundle\Component\Document\Generator\Company\Pdf as PdfGenerator;
use BrBundle\Entity\Company;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Imagick;
use Laminas\Filter\File\RenameUpload as RenameUploadFilter;
use Laminas\Http\Headers;
use Laminas\Validator\File\IsImage as IsImageValidator;
use Laminas\Validator\File\UploadFile as UploadFileValidator;
use Laminas\Validator\ValidatorChain;
use Laminas\View\Model\ViewModel;

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
        $paginator = null;
        if ($this->getParam('field') !== null) {
            $companies = $this->search();
            if ($companies === null) {
                return new ViewModel();
            }

            $paginator = $this->paginator()->createFromQuery(
                $companies,
                $this->getParam('page')
            );
        }

        if ($paginator === null) {
            $paginator = $this->paginator()->createFromEntity(
                'BrBundle\Entity\Company',
                $this->getParam('page'),
                array(
                    'active' => true,
                ),
                array(
                    'name' => 'ASC',
                )
            );
        }

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('br_company_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $company = $form->hydrateObject();
                $this->getEntityManager()->persist($company);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The company was successfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_company',
                    array(
                        'action' => 'manage',
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
        $company = $this->getCompanyEntity();
        if ($company === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_company_edit', array('company' => $company));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The company was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'br_admin_company',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'company' => $company,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $company = $this->getCompanyEntity();
        if ($company === null) {
            return new ViewModel();
        }

        $company->deactivate();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function uploadAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getRequest()->getPost();

            $validatorChain = new ValidatorChain();
            $validatorChain->attach(new UploadFileValidator());
            if ($form['type'] == 'image') {
                $validatorChain->attach(
                    new IsImageValidator(
                        array('image/gif', 'image/jpeg', 'image/png')
                    )
                );
            }

            $file = $this->getRequest()->getFiles()['file'];
            if ($validatorChain->isValid($file)) {
                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.file_path') . '/';

                do {
                    $fileName = sha1(uniqid());
                } while (file_exists($filePath . $fileName));

                $renameUploadFilter = new RenameUploadFilter();
                $renameUploadFilter->setTarget($filePath . $fileName)
                    ->filter($file);

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
                        ),
                    )
                );
            }
        }

        return new ViewModel();
    }

    public function editLogoAction()
    {
        $company = $this->getCompanyEntity();
        if ($company === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_company_logo');

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            if ($form->isValid()) {
                $formData = $form->getData();
                $image = new Imagick($formData['logo']['tmp_name']);
                $image->thumbnailImage(320, 320, true);

                if ($company->getLogo() != '' || $company->getLogo() !== null) {
                    $fileName = '/' . $company->getLogo();
                } else {
                    do {
                        $fileName = '/' . sha1(uniqid());
                    } while (file_exists($filePath . $fileName));
                }
                $image->writeImage('public/' . $filePath . $fileName);
                $company->setLogo($fileName);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The company\'s logo has successfully been updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_company',
                    array(
                        'action' => 'editLogo',
                        'id'     => $company->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company'  => $company,
                'form'     => $form,
                'logoPath' => $filePath,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $companies = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($companies as $company) {
            $item = (object) array();
            $item->id = $company->getId();
            $item->name = $company->getName();
            $item->vatNumber = $company->getVatNumber();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function csvAction()
    {
        $file = new CsvFile();
        $heading = array('Company Name', 'Company Phone Number', 'Name', 'Username', 'E-mail', 'Contact Phone Number');

        $companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        $results = array();
        foreach ($companies as $company) {
            $company_users = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\User\Person\Corporate')
                ->findBy(
                    array(
                        'canLogin' => 'true',
                        'company'  => $company->getId(),
                    )
                );

            foreach ($company_users as $user) {
                $results[] = array($company->getName(), $company->getPhoneNumber(), $user->getFullName(), $user->getUsername(), $user->getEmail(), ' ' . $user->getPhoneNumber());
            }
            if (count($company_users) == 0) {
                $results[] = array($company->getName(), $company->getPhoneNumber(), '/', '/', '/', '/');
            }
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="contacts_list.csv"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function pdfAction()
    {
        $file = new TmpFile();
        $document = new PdfGenerator($this->getEntityManager(), $file);
        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="contacts_list.pdf"',
                'Content-Type'        => 'application/pdf',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAllByNameQuery($this->getParam('string'))
            ->setMaxResults(10)
            ->getResult();

        $result = array();
        foreach ($companies as $company) {
            $item = (object) array();
            $item->id = $company->getId();
            $item->name = $company->getName();
            $item->value = $company->getName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return Company|null
     */
    private function getCompanyEntity()
    {
        $company = $this->getEntityById('BrBundle\Entity\Company');

        if (!($company instanceof Company)) {
            $this->flashMessenger()->error(
                'Error',
                'No company was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $company;
    }
}
