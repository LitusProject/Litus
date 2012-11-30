<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace BannerBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    BannerBundle\Entity\Nodes\Banner,
    BannerBundle\Form\Admin\Banner\Add as AddForm,
    BannerBundle\Form\Admin\Banner\Edit as EditForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Http\Headers,
    Zend\Validator\File\Count as CountValidator,
    Zend\Validator\File\Size as SizeValidator,
    Zend\Validator\File\IsImage as ImageValidator,
    Zend\Validator\File\ImageSize as ImageSizeValidator,
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

    const BANNER_WIDTH = 940;
    const BANNER_HEIGHT = 100;

    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BannerBundle\Entity\Nodes\Banner',
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
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            $upload = new FileUpload();

            $upload->addValidator(new SizeValidator(array('max' => '10MB')));
            $upload->addValidator(new ImageValidator());
            $validator = new ImageSizeValidator();
            $validator->setMinWidth($this::BANNER_WIDTH)
                ->setMinHeight($this::BANNER_HEIGHT)
                ->setMaxWidth($this::BANNER_WIDTH)
                ->setMaxHeight($this::BANNER_HEIGHT);
            $upload->addValidator($validator);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($upload->isValid()) {

                    $filePath = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('banner.image_path');

                    $fileName = '';
                    do{
                        $fileName = '/' . sha1(uniqid());
                    } while (file_exists($filePath . $fileName));

                    $upload->addFilter('Rename', $filePath . $fileName);
                    $upload->receive();

                    $banner = new Banner(
                        $this->getAuthentication()->getPersonObject(),
                        $formData['name'],
                        $fileName,
                        DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                        DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                        $formData['active'],
                        $formData['url']
                    );
                    $this->getEntityManager()->persist($banner);

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Succes',
                            'The banner was successfully added!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'admin_banner',
                        array(
                            'action' => 'manage'
                        )
                    );

                    return new ViewModel();
                }
            }

            if (!$upload->isValid()) {
                $dataError = $upload->getMessages();
                $error = array();

                foreach($dataError as $key=>$row)
                    $error[] = $row;

                $form->setMessages(array('file'=>$error ));
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
        if (!($banner = $this->_getBanner()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $banner);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);


            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $upload = new FileUpload();

                $upload->addValidator(new SizeValidator(array('max' => '10MB')));
                $upload->addValidator(new ImageValidator());
                $validator = new ImageSizeValidator();
                $validator->setMinWidth($this::BANNER_WIDTH)
                    ->setMinHeight($this::BANNER_HEIGHT)
                    ->setMaxWidth($this::BANNER_WIDTH)
                    ->setMaxHeight($this::BANNER_HEIGHT);
                $upload->addValidator($validator);

                if (!$upload->isUploaded(array('file')) || $upload->isValid()) {

                    $banner->setName($formData['name'])
                        ->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                        ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']))
                        ->setActive($formData['active'])
                        ->setUrl($formData['url']);

                    if ($upload->isUploaded(array('file'))) {

                        $filePath = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('banner.image_path');

                        $fileName = '';
                        do{
                            $fileName = '/' . sha1(uniqid());
                        } while (file_exists($filePath . $fileName));

                        $upload->addFilter('Rename', $filePath . $fileName);
                        $upload->receive();

                        $banner->setImage($fileName);
                    }

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Succes',
                            'The banner was successfully edited!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'admin_banner',
                        array(
                            'action' => 'manage'
                        )
                    );

                    return new ViewModel();
                } else {
                    $dataError = $upload->getMessages();
                    $error = array();

                    foreach($dataError as $key=>$row)
                        $error[] = $row;

                    $form->setMessages(array('file'=>$error ));
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function progressAction()
    {
        $uploadId = ini_get('session.upload_progress.prefix') . $this->getRequest()->getPost()->get('upload_id');

        return new ViewModel(
            array(
                'result' => isset($_SESSION[$uploadId]) ? $_SESSION[$uploadId] : '',
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

    private function _getBanner()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the banner!'
                )
            );

            $this->redirect()->toRoute(
                'admin_banner',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $banner = $this->getEntityManager()
            ->getRepository('BannerBundle\Entity\Nodes\Banner')
            ->findOneById($this->getParam('id'));

        if (null === $banner) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No banner with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_banner',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $banner;
    }
}
