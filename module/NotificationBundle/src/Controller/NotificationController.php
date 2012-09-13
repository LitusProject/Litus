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

namespace NotificationBundle\Controller;

use DateTime,
    Zend\View\Model\ViewModel;

/**
 * NotificationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NotificationController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'NotificationBundle\Entity\Nodes\Notification',
            $this->getParam('page'),
            array(),
            array(
                'creationTime' => 'DESC',
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function viewAction()
    {
        if (!($news = $this->_getNews())) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'news' => $news,
            )
        );
    }

    public function _getNews()
    {
        if (null === $this->getParam('name'))
            return;

        $news = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Nodes\News')
            ->findOneByName($this->getParam('name'));

        if (null === $news)
            return;

        return $news;
    }
}
