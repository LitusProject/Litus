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

namespace BannerBundle\Controller\Admin;

use BannerBundle\Entity\Node\Banner;
use Zend\View\Model\ViewModel;

/**
 * BannerController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class BannerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BannerBundle\Entity\Node\Banner',
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('banner_banner_add');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'banner_admin_banner',
                array(
                    'action' => 'upload',
                )
            )
        );

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $banner = $this->getBannerEntity();
        if ($banner === null) {
            return new ViewModel();
        }

        $form = $this->getForm('banner_banner_edit', $banner);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'banner_admin_banner',
                array(
                    'action' => 'upload',
                    'id'     => $banner->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    private function receive($file, Banner $banner)
    {
        do {
            $image = sha1(uniqid());
            $path = $this->getStoragePath('banner_banners_images', $image);
        } while ($this->getFilesystem()->has($path));

        $stream = fopen($file['tmp_name'], 'r+');
        $this->getFilesystem()->writeStream($path, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        $banner->setImage($image);
    }

    public function uploadAction()
    {
        $this->initAjax();

        $form = $this->getForm('banner_banner_add');

        $banner = $this->getEntityById('BannerBundle\Entity\Node\Banner');
        if ($banner !== null) {
            $form = $this->getForm('banner_banner_edit', $banner);
        }

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            if ($form->isValid()) {
                $formData = $form->getData();

                if ($banner === null) {
                    $banner = $form->hydrateObject();

                    $this->receive($formData['file'], $banner);

                    $this->getEntityManager()->persist($banner);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The banner was successfully added!'
                    );

                    return new ViewModel(
                        array(
                            'status' => 'success',
                            'info'   => array(
                                'info' => (object) array(
                                    'name' => $banner->getName(),
                                ),
                            ),
                        )
                    );
                } else {
                    if (isset($formData['file'])) {
                        $path = $this->getStoragePath(
                            'banner_banners_images',
                            $banner->getImage()
                        );

                        if ($this->getFilesystem()->has($path)) {
                            $this->getFilesystem()->delete($path);
                        }

                        $this->receive($formData['file'], $banner);
                    }

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Succes',
                        'The banner was successfully edited!'
                    );

                    return new ViewModel(
                        array(
                            'status' => 'success',
                            'info'   => array(
                                'info' => (object) array(
                                    'name' => $banner->getName(),
                                ),
                            ),
                        )
                    );
                }
            } else {
                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form'   => array(
                            'errors' => $form->getMessages(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'status' => 'error',
                'form'   => array(
                    'errors' => array(),
                ),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $banner = $this->getBannerEntity();
        if ($banner === null) {
            return new ViewModel();
        }

        $this->getFilesystem()->delete(
            Banner::getImagePath($banner->getImage())
        );

        $this->getEntityManager()->remove($banner);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Banner|null
     */
    private function getBannerEntity()
    {
        $banner = $this->getEntityById('BannerBundle\Entity\Node\Banner');

        if (!($banner instanceof Banner)) {
            $this->flashMessenger()->error(
                'Error',
                'No banner was found!'
            );

            $this->redirect()->toRoute(
                'banner_admin_banner',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $banner;
    }
}
