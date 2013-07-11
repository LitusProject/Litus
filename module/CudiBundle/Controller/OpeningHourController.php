<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CudiBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    DateInterval,
    PageBundle\Entity\Node\Page,
    Zend\View\Model\ViewModel;

/**
 * OpeningHourController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OpeningHourController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function weekAction()
    {
        try {
            $id = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.opening_hours_page');

            $link = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Link')
                ->findOneById($id);

            if (null !== $link)
                $page = $link->getParent();
        } catch(\Exception $e) {}

        if (isset($page)) {
            $submenu = $this->_buildSubmenu($page);
            if (empty($submenu) && null !== $page->getParent())
                $submenu = $this->_buildSubmenu($page->getParent());
        }

        $openingHours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findCurrentWeek();

        $start = new DateTime();
        $start->setTime(0, 0);
        if ($start->format('N') > 5)
            $start->add(new DateInterval('P' . (8 - $start->format('N')) .'D'));
        else
            $start->sub(new DateInterval('P' . ($start->format('N') - 1) .'D'));

        $startHour = 12;
        $endHour = 20;

        $week = array();
        $openingHoursArray = array();
        $start->sub(new DateInterval('P1D'));
        for($i = 0 ; $i < 5 ; $i ++) {
            $start->add(new DateInterval('P1D'));
            $week[] = clone $start;
            $openingHoursArray[$i] = array();
        }

        foreach($openingHours as $openingHour) {
            if ($openingHour->getStart()->format('H') < $startHour)
                $startHour = $openingHour->getStart()->format('H');

            if ($openingHour->getEnd()->format('H') > $endHour)
                $endHour = $openingHour->getEnd()->format('H');

            $openingHoursArray[$openingHour->getStart()->format('N') - 1][] = $openingHour;
        }

        return new ViewModel(
            array(
                'openingHours' => $openingHours,
                'openingHoursTimeline' => $openingHoursArray,
                'week' => $week,
                'startHour' => $startHour,
                'endHour' => $endHour,
                'submenu' => isset($submenu) ? $submenu : null,
                'page' => isset($page) ? $page : null,
                'link' => isset($link) ? $link : null,
            )
        );
    }

    private function _buildSubmenu(Page $page)
    {
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findByParent($page->getId());

        $links = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Link')
            ->findByParent($page->getId());

        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findByParent($page->getId());

        $submenu = array();
        foreach ($pages as $page) {
            $submenu[] = array(
                'type'     => 'page',
                'name'     => $page->getName(),
                'parent'   => $page->getParent()->getName(),
                'title'    => $page->getTitle($this->getLanguage())
            );
        }

        foreach ($links as $link) {
            $submenu[] = array(
                'type' => 'link',
                'id'   => $link->getId(),
                'name' => $link->getName($this->getLanguage())
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
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findByCategory($category);

            $links = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Link')
                ->findByCategory($category);

            foreach ($pages as $page) {
                $submenu[$i]['items'][] = array(
                    'type'  => 'page',
                    'name'  => $page->getName(),
                    'title' => $page->getTitle($this->getLanguage())
                );
            }

            foreach ($links as $link) {
                $submenu[$i]['items'][] = array(
                    'type' => 'link',
                    'id'   => $link->getId(),
                    'name' => $link->getName($this->getLanguage())
                );
            }

            $sort = array();
            foreach ($submenu[$i]['items'] as $key => $value)
                $sort[$key] = isset($value['title']) ? $value['title'] : $value['name'];

            array_multisort($sort, $submenu[$i]['items']);

            $i++;
        }

        $sort = array();
        foreach ($submenu as $key => $value)
            $sort[$key] = isset($value['title']) ? $value['title'] : $value['name'];

        array_multisort($sort, $submenu);

        return $submenu;
    }
}