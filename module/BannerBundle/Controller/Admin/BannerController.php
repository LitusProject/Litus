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

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    BannerBundle\Entity\Node\Banner,
    Zend\File\Transfer\Adapter\Http as FileUpload,
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

    private function receive(FileUpload $upload, Banner $banner)
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('banner.image_path');

        $fileName = '';
        do {
            $fileName = '/' . sha1(uniqid());
        } while (file_exists($filePath . $fileName));

        $upload->addFilter('Rename', $filePath . $fileName);
        $upload->receive();

        $banner->setImage($fileName);
    }

    public function uploadAction()
    {
        $this->initAjax();

        $form = $this->getForm('banner_banner_add');

        $upload = new FileUpload();
        $upload->setValidators($form->getInputFilter()->get('file')->getValidatorChain()->getValidators());

        if (!($banner = $this->_getBanner(false))) {
            $form = $this->getForm('banner_banner_add');
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid() && $upload->isValid()) {
                $formData = $form->getFormData($formData);

                $banner = new Banner(
                    $this->getAuthentication()->getPersonObject()
                );
                $form->hydrateObject($banner);
                $this->receive($upload, $banner);

                $this->getEntityManager()->persist($banner);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The banner was successfully added!'
                    )
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
            } else {
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

                if (sizeof($upload->getMessages()) > 0)
                    $formErrors['file'] = $upload->getMessages();

                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form' => array(
                            'errors' => $formErrors,
                        ),
                    )
                );
            }
        } else {
            $form = $this->getForm('banner_banner_edit', $banner);
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                if ($upload->isValid()) {
                    $this->receive($upload, $banner);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The banner was successfully edited!'
                    )
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
            } else {
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
        }

        return new ViewModel();
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

    private function _getBanner($redirect = true)
    {
        if (null === $this->getParam('id')) {
            if ($redirect) {
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::ERROR,
                        'Error',
                        'No ID was given to identify the banner!'
                    )
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
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::ERROR,
                        'Error',
                        'No banner with the given ID was found!'
                    )
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
