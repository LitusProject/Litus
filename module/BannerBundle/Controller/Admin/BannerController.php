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

namespace BannerBundle\Controller\Admin;

use BannerBundle\Entity\Node\Banner,
    Zend\View\Model\ViewModel;

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
                'paginator' => $paginator,
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
        if (!($banner = $this->getBannerEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('banner_banner_edit', $banner);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'banner_admin_banner',
                array(
                    'action' => 'upload',
                    'id' => $banner->getId(),
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
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('banner.image_path');

        do {
            $fileName = '/' . sha1(uniqid());
        } while (file_exists($filePath . $fileName));

        rename($file['tmp_name'], $filePath . $fileName);

        $banner->setImage($fileName);
    }

    public function uploadAction()
    {
        $this->initAjax();

        $isNew = !($banner = $this->getBannerEntity(false));

        if ($isNew) {
            $form = $this->getForm('banner_banner_add');
        } else {
            $form = $this->getForm('banner_banner_edit', $banner);
        }

        if ($this->getRequest()->isPost()) {
            $form->setData(array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            ));

            if ($form->isValid()) {
                $formData = $form->getData();

                if ($isNew) {
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
                            'info' => array(
                                'info' => (object) array(
                                    'name' => $banner->getName(),
                                ),
                            ),
                        )
                    );
                } elseif (!$isNew) {
                    if (isset($formData['file'])) {
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
                            'info' => array(
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
                        'form' => array(
                            'errors' => $form->getMessages(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'status' => 'error',
                'form' => array(
                    'errors' => array(),
                ),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($banner = $this->getBannerEntity())) {
            return new ViewModel();
        }

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
     * @param  boolean     $redirect
     * @return Banner|null
     */
    private function getBannerEntity($redirect = true)
    {
        $banner = $this->getEntityById('BannerBundle\Entity\Node\Banner');

        if (!($banner instanceof Banner) && $redirect) {
            $this->flashMessenger()->error(
                'Error',
                'No baner was found!'
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
