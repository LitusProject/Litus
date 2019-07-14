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

namespace CudiBundle\Controller\Sale;

use Zend\View\Model\ViewModel;

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
                'logos' => $logos,
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
