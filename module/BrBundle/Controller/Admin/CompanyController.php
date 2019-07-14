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

namespace BrBundle\Controller\Admin;

use BrBundle\Component\Document\Generator\Company\Pdf as PdfGenerator;
use BrBundle\Entity\Company;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Imagick;
use Zend\Filter\File\RenameUpload as RenameUploadFilter;
use Zend\Http\Headers;
use Zend\Validator\File\IsImage as IsImageValidator;
use Zend\Validator\File\UploadFile as UploadFileValidator;
use Zend\Validator\ValidatorChain;
use Zend\View\Model\ViewModel;

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

    public function editLogoAction()
    {
        $company = $this->getCompanyEntity();
        if ($company === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_company_logo');

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            if ($form->isValid()) {
                $formData = $form->getData();

                $tmpFile = new TmpFile();
                $image = new Imagick($formData['logo']['tmp_name']);
                $image->thumbnailImage(320, 320, true);
                $image->writeImage($tmpFile->getFilename());

                if ($company->getLogo() !== null) {
                    $path = $this->getPath(
                        'br_companies_logos',
                        $company->getLogo()
                    );

                    if ($this->getFilesystem()->has($path)) {
                        $this->getFilesystem()->delete($path);
                    }
                }

                do {
                    $logo = sha1(uniqid());
                    $path = $this->getStoragePath('br_companies_logos', $logo);
                } while ($this->getFilesystem()->has($path));

                $stream = fopen($tmpFile->getFilename(), 'r+');
                $this->getFilesystem()->writeStream($path, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }

                $company->setLogo($logo);

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
                'company' => $company,
                'form'    => $form,
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
