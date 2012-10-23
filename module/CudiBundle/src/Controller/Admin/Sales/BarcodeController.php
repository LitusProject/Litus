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

namespace CudiBundle\Controller\Admin\Sales;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Sales\Articles\Barcode,
    CudiBundle\Form\Admin\Sales\Barcodes\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * BarcodeController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BarcodeController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($article = $this->_getSaleArticle()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);
                
                $article->addAdditionalBarcode(new Barcode($article, $formData['barcode']));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The barcode was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_sales_barcode',
                    array(
                        'action' => 'manage',
                        'id' => $article->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $barcodes = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Articles\Barcode')
            ->findAllByArticle($article, $this->getCurrentAcademicYear());

        return new ViewModel(
            array(
                'article' => $article,
                'barcodes' => $barcodes,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($barcode = $this->_getBarcode()))
            return new ViewModel();

        $this->getEntityManager()->remove($barcode);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getSaleArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the article!'
                )
            );

            $this->redirect()->toRoute(
                'admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }

    private function _getBarcode()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the barcode!'
                )
            );

            $this->redirect()->toRoute(
                'admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $barcode = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Articles\Barcode')
            ->findOneById($this->getParam('id'));

        if (null === $barcode) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No barcode with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $barcode;
    }
}
