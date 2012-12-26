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

namespace CudiBundle\Controller\Sale2;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Sales\Returned as ReturnedLog,
    CudiBundle\Entity\Sales\QueueItem,
    CudiBundle\Form\Sale\Sale\ReturnSale as ReturnSaleForm,
    Zend\View\Model\ViewModel;

/**
 * SaleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CudiBundle\Component\Controller\SaleController
{
    public function saleAction()
    {
        $barcodePrefix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_item_barcode_prefix');

        $enableCollectScanning = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        $paydesks = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\PayDesk')
            ->findBy(array(), array('name' => 'ASC'));

        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.queue_socket_key'),
                'barcodePrefix' => $barcodePrefix,
                'paydesks' => $paydesks,
                'enableCollectScanning' => $enableCollectScanning,
            )
        );
    }

    public function returnAction()
    {
        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneById($this->getParam('session'));

        $form = new ReturnSaleForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\Person')
                    ->findOneById($formData['person_id']);

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findOneByBarcode($formData['article']);

                $booking = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Booking')
                    ->findOneSoldByPersonAndArticle($person, $article);

                if ($booking) {
                    $saleItem = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sales\SaleItem')
                        ->findOneByPersonAndArticle($person, $article);

                    if ($saleItem) {
                        if ($saleItem->getNumber() == 1) {
                            $this->getEntityManager()->remove($saleItem);
                        } else {
                            $saleItem->setNumber($saleItem->getNumber() - 1);
                        }
                    }

                    if ($booking->getNumber() == 1) {
                        $this->getEntityManager()->remove($booking);
                    } else {
                        $booking->setNumber($booking->getNumber() - 1);
                    }

                    $article->setStockValue($article->getStockValue() + 1);

                    $this->getEntityManager()->persist(new ReturnedLog($this->getAuthentication()->getPersonObject(), $article));

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'The sale was successfully returned!'
                        )
                    );
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'ERROR',
                            'The sale could not be returned!'
                        )
                    );
                }

                $this->redirect()->toRoute(
                    'sale_sale',
                    array(
                        'action' => 'return',
                        'session' => $session->getId(),
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

    /**
     * Returns the WebSocket URL.
     *
     * @return string
     */
    protected function getSocketUrl()
    {
        $address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_remote_host');
        $port = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_port')-100;

        return 'ws://' . $address . ':' . $port;
    }
}
