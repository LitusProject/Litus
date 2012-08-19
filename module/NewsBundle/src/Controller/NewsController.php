<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace NewsBundle\Controller;

use DateTime,
    Zend\View\Model\ViewModel;

/**
 * NewsController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NewsController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'NewsBundle\Entity\Nodes\News',
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
        if (!($news = $this->_getNews()))
            return new ViewModel();

        return new ViewModel(
            array(
                'news' => $news,
            )
        );
    }

    public function _getNews()
    {
        if (null === $this->getParam('name')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $news = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Nodes\News')
            ->findOneByName($this->getParam('name'));

        if (null === $news) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return $news;
    }
}
