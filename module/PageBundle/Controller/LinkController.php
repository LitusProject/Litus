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

namespace PageBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    PageBundle\Entity\Nodes\Page,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * LinkController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class LinkController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        if (!($link = $this->_getLink())) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        $this->redirect()->toUrl($link->getUrl($this->getLanguage()));

        return new ViewModel();
    }

    private function _getLink()
    {
        if (null === $this->getParam('id'))
            return;

        $link = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Link')
            ->findOneById($this->getParam('id'));

        if (null === $link)
            return;

        return $link;
    }
}
