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

namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company,
    BrBundle\Entity\Company\Logo,
    Imagick,
    Zend\View\Model\ViewModel;

/**
 * LogoController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class LogoController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($company = $this->getCompanyEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Company\Logo',
            $this->getParam('page'),
            array(
                'company' => $company,
            )
        );

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'company' => $company,
                'filePath' => $filePath,
            )
        );
    }

    private function receive($file, Logo $logo)
    {
        $filePath = 'public/' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path') . '/';

        $image = new Imagick($file['tmp_name']);
        $image->setImageFormat('png');
        $image->scaleImage(1000, 100, true);

        $original = clone $image;

        $image->modulateImage(100, 0, 100);

        $color = 0;
        $iterator = $image->getPixelIterator();
        $nbPixels = 0;
        foreach ($iterator as $pixels) {
            foreach ($pixels as $pixel) {
                if ($pixel->getColor()['a'] == 1) {
                    continue;
                }

                $pixel_color = $pixel->getColor(true);
                $nbPixels++;
                $color += ($pixel_color['r'] + $pixel_color['g'] + $pixel_color['b']) / 3;
            }
        }
        if ($nbPixels != 0 && $color / $nbPixels < 0.5) {
            $original->evaluateImage(Imagick::EVALUATE_ADD, 800 / ($color / $nbPixels));
        }

        $all = new Imagick();
        $all->addImage($original);
        $all->addImage($image);
        $all->resetIterator();
        $combined = $all->appendImages(true);
        $combined->setImageFormat('png');

        do {
            $fileName = sha1(uniqid());
        } while (file_exists($filePath . $fileName));
        $combined->writeImage($filePath . $fileName);

        $logo->setPath($fileName)
            ->setWidth($image->getImageWidth())
            ->setHeight($image->getImageHeight());
    }

    public function addAction()
    {
        if (!($company = $this->getCompanyEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('br_company_logo_add', array('company' => $company));

        if ($this->getRequest()->isPost()) {
            $form->setData(array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            ));

            if ($form->isValid()) {
                $formData = $form->getData();

                $logo = $form->hydrateObject(
                    new Logo($company)
                );
                $this->receive($formData['logo'], $logo);

                $this->getEntityManager()->persist($logo);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The logo has successfully been added!'
                );

                $this->redirect()->toRoute(
                    'br_admin_company_logo',
                    array(
                        'action' => 'manage',
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
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($logo = $this->getLogoEntity())) {
            return new ViewModel();
        }

        $filePath = 'public/' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path') . '/';

        if (file_exists($filePath . $logo->getPath())) {
            unlink($filePath . $logo->getPath());
        }

        $this->getEntityManager()->remove($logo);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
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

    /**
     * @return Logo|null
     */
    private function getLogoEntity()
    {
        $logo = $this->getEntityById('BrBundle\Entity\Company\Logo');

        if (!($logo instanceof Logo)) {
            $this->flashMessenger()->error(
                'Error',
                'No logo was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $logo;
    }
}
