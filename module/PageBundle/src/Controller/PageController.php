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

namespace PageBundle\Controller;

use PageBundle\Entity\Nodes\Page,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * PageController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class PageController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        if (!($page = $this->_getPage()))
            return new ViewModel();
echo $page->getId();
        return new ViewModel(
            array(
                'page' => $page,
                'submenu' => $this->_buildSubmenu($page)
            )
        );
    }

    public function fileAction()
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('page.file_path') . '/' . $this->getParam('name');

        if ($this->getParam('name') == '' || !file_exists($filePath)) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="' . $this->getParam('name') . '"',
            'Content-type' => mime_content_type($filePath),
            'Content-Length' => filesize($filePath),
        ));
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath, 'r');
        $data = fread($handle, filesize($filePath));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    private function _buildSubmenu(Page $page)
    {
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Nodes\Page')
            ->findByParent($page->getId());

        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findByParent($page->getId());

        $submenu = array();
        foreach ($pages as $page) {
            $submenu[] = array(
                'type'  => 'page',
                'name'  => $page->getName(),
                'title' => $page->getTitle($this->getLanguage())
            );
        }

        $i = count($submenu);
        foreach ($categories as $category) {
            $submenu[$i] = array(
                'type'  => 'category',
                'name'  => $category->getName(),
                'items' => array()
            );

            $pages = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Nodes\Page')
                ->findByCategory($category);

            foreach ($pages as $page) {
                $submenu[$i]['items'][] = array(
                    'type'  => 'page',
                    'name'  => $page->getName(),
                    'title' => $page->getTitle($this->getLanguage())
                );

                $sort = array();
                foreach ($submenu[$i]['items'] as $key => $value)
                    $sort[$key] = $value['title'];

                array_multisort($sort, $submenu[$i]['items']);
            }

            $i++;
        }

        $sort = array();
        foreach ($submenu as $key => $value)
            $sort[$key] = isset($value['title'])? $value['title'] : $value['name'];

        array_multisort($sort, $submenu);

        return $submenu;
    }

    private function _getPage()
    {
        if (null === $this->getParam('name')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No name was given to identify the page!'
                )
            );

            $this->redirect()->toRoute(
                'page',
                array(
                    'action' => 'view'
                )
            );

            return;
        }

        $page = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Nodes\Page')
            ->findOneBy(array('name' => $this->getParam('name'), 'endTime' => null));

        if (null === $page) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No page with the given name was found!'
                )
            );

            $this->redirect()->toRoute(
                'page',
                array(
                    'action' => 'view'
                )
            );

            return;
        }

        return $page;
    }
}
