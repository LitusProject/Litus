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

namespace PageBundle\Controller;

use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use PageBundle\Entity\Node\Page;

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
        $page = $this->getPageEntity();
        if ($page === null) {
            return $this->notFoundAction();
        }

        $submenu = $this->buildSubmenu($page);
        $parent = $page->getParent();
        if (count($submenu) == 0 && $parent !== null) {
            $submenu = $this->buildSubmenu($parent);
        }

        $enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.enable_faq');

        $faqs = $enabled ? $this->getMaps($page) : null;

        return new ViewModel(
            array(
                'page'    => $page,
                'submenu' => $submenu,
                'faqs'    => $faqs,
            )
        );
    }

    public function fileAction()
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('page.file_path') . '/' . $this->getParam('name');

        if ($this->getParam('name') == '' || !file_exists($filePath)) {
            return $this->notFoundAction();
        }

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'inline; filename="' . $this->getParam('name') . '"',
                'Content-Type'        => mime_content_type($filePath),
                'Content-Length'      => filesize($filePath),
            )
        );
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

    /**
     * Returns all the faqs for this page, in an array (id, title, content) in the correct language.
     * @param Page $page
     */
    private function getMaps(Page $page)
    {

        $maps = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQPageMap')
            ->findAllByPageQuery($page)->getResult();

        $allMaps = array();
        $lang = $this->getLanguage();

        foreach ($maps as $map) {
            $allMaps[] = array(
                'id'        => $map->getId(),
                'title'     => $map->getFAQ()->getTitle($lang),
                'content'   => $map->getFAQ()->getContent($lang),
            );
        }

        return $allMaps;
    }

    /**
     * @return Page|null
     */
    private function getPageEntity()
    {
        $page = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findOneByNameAndParent(
                $this->getParam('name', ''),
                $this->getParam('parent')
            );

        if (!($page instanceof Page)) {
            return;
        }

        return $page;
    }
}
