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
        if (!($banner = $this->_getBanner()))
            return new ViewModel();

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

        $isNew = !($banner = $this->_getBanner(false));

        if ($isNew)
            $form = $this->getForm('banner_banner_add');
        else
            $form = $this->getForm('banner_banner_edit', $banner);

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
                    if (isset($formData['file']))
                        $this->receive($formData['file'], $banner);

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
            }

            $errors = $form->getMessages();
            $formErrors = array();

            foreach ($form->getElements() as $key => $element) {
                if (!isset($errors[$element->getName()]))
                    continue;

                $formErrors[$element->getAttribute('id')] = array();

                foreach ($errors[$element->getName()] as $error) {
                    $formErrors[$element->getAttribute('id')][] = $error;
                }
            }

            return new ViewModel(
                array(
                    'status' => 'error',
                    'form' => array(
                        'errors' => $formErrors,
                    ),
                )
            );
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

        if (!($banner = $this->_getBanner()))
            return new ViewModel();

        $this->getEntityManager()->remove($banner);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    /**
     * @return Banner|null
     */
    private function _getBanner($redirect = true)
    {
        if (null === $this->getParam('id')) {
            if ($redirect) {
                $this->flashMessenger()->error(
                    'Error',
                    'No ID was given to identify the banner!'
                );

                $this->redirect()->toRoute(
                    'banner_admin_banner',
                    array(
                        'action' => 'manage'
                    )
                );
            }

            return;
        }

        $banner = $this->getEntityManager()
            ->getRepository('BannerBundle\Entity\Node\Banner')
            ->findOneById($this->getParam('id'));

        if (null === $banner) {
            if ($redirect) {
                $this->flashMessenger()->error(
                    'Error',
                    'No banner with the given ID was found!'
                );

                $this->redirect()->toRoute(
                    'banner_admin_banner',
                    array(
                        'action' => 'manage'
                    )
                );
            }

            return;
        }

        return $banner;
    }
}
