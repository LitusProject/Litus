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
    CudiBundle\Entity\Sales\QueueItem,
    CudiBundle\Form\Sale\Queue\SignIn as SignInForm,
    Zend\View\Model\ViewModel;

/**
 * QueueController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueueController extends \CudiBundle\Component\Controller\SaleController
{
    public function overviewAction()
    {
        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.queue_socket_key'),
            )
        );
    }

    public function screenAction()
    {
        $logos = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Logo')
            ->findAllByType('cudi');

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        $payDesks = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\PayDesk')
            ->findBy(array(), array('name' => 'ASC'));

        for ($i = 0 ; $i < sizeof($payDesks) ; $i++) {
            if (strpos('paydesk', $payDesks[$i]->getCode()) !== 0)
                unset($payDesks[$i]);
        }

        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.queue_socket_key'),
                'payDesks' => $payDesks,
                'logos' => $logos,
                'logoPath' => $logoPath,
            )
        );
    }

    public function signInAction()
    {
        $form = new SignInForm();

        return new ViewModel(
            array(
                'form' => $form,
                'socketUrl' => $this->getSocketUrl(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.queue_socket_key'),
            )
        );
    }
}
