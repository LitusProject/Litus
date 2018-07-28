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

namespace TicketBundle\Component\Controller;

use Exception,
    Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    /**
     * Execute the request.
     *
     * @param  MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($this->getParam('id'));

        if (null == $event) {
            throw new Exception('No valid event is given');
        }

        $result = parent::onDispatch($e);

        $language = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $result->language = $language;
        $result->event = $event;

        $result->organizationUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_url');

        $e->setResult($result);

        return $result;
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return array(
            'action'     => 'index',
            'controller' => 'common_index',

            'auth_route'     => 'ticket_sale_index',
            'redirect_route' => 'ticket_sale_index',
        );
    }

    /**
     * @return \CommonBundle\Component\Form\Factory
     */
    protected function getFormFactory()
    {
        return $this->getServiceLocator()->get('formfactory.bootstrap');
    }
}
