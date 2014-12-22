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

namespace BrBundle\Controller\Admin;





use BrBundle\Entity\Company,
    Imagick,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Validator\File\IsImage as IsImageValidator,
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
        if (null === $this->getParam('field')) {
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
        } else {
            $paginator = $this->paginator()->createFromQuery(
                $this->_search(),
                $this->getParam('page')
            );
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
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
        if (!($company = $this->_getCompany())) {
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
                'form' => $form,
                'company' => $company,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($company = $this->_getCompany())) {
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
            $formData = $this->getRequest()->getPost();

            $upload = new FileUpload();

            if ('image' == $formData['type']) {
                $upload->addValidator(new IsImageValidator(array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif')));
            }

            if ($upload->isValid()) {
                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.file_path') . '/';

                do {
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
                        ),
                    )
                );
            }
        }

        return new ViewModel();
    }

    public function editLogoAction()
    {
        if (!($company = $this->_getCompany())) {
            return new ViewModel();
        }

        $form = $this->getForm('br_company_logo');

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        if ($this->getRequest()->isPost()) {
            $form->setData(array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            ));

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

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $companies = $this->_search()
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

    private function _search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return Company
     */
    private function _getCompany()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the company!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->error(
                'Error',
                'No company with the given ID was found!'
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

    private function _getCompanyByLogo()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the company!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneByLogo($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->error(
                'Error',
                'No company with the given ID was found!'
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
