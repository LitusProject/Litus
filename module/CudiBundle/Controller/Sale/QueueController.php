<?php

namespace CudiBundle\Controller\Sale;

use Laminas\View\Model\ViewModel;

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
                'socketUrl'   => $this->getSocketUrl(),
                'authSession' => $this->getAuthentication()
                    ->getSessionObject(),
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
            ->getRepository('CudiBundle\Entity\Sale\PayDesk')
            ->findBy(array(), array('name' => 'ASC'));

        $nbPayDesks = count($payDesks);
        for ($i = 0; $i < $nbPayDesks; $i++) {
            if (strpos($payDesks[$i]->getCode(), 'paydesk') !== 0) {
                unset($payDesks[$i]);
            }
        }

        return new ViewModel(
            array(
                'socketUrl'   => $this->getSocketUrl(),
                'authSession' => $this->getAuthentication()
                    ->getSessionObject(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.queue_socket_key'),
                'payDesks'         => $payDesks,
                'enableCollecting' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.enable_collect_scanning'),
                'logos'    => $logos,
                'logoPath' => $logoPath,
            )
        );
    }

    public function signInAction()
    {
        $form = $this->getForm('cudi_sale_queue_sign-in');

        return new ViewModel(
            array(
                'form'        => $form,
                'socketUrl'   => $this->getSocketUrl(),
                'authSession' => $this->getAuthentication()
                    ->getSessionObject(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.queue_socket_key'),
            )
        );
    }
}
