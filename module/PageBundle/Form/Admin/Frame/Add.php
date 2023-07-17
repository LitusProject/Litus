<?php

namespace PageBundle\Form\Admin\Frame;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;
use PageBundle\Entity\Node\CategoryPage as CategoryPageEntity;
use PageBundle\Entity\Frame as FrameEntity;

/**
 * Add Frame
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'PageBundle\Hydrator\Frame';

    /**
     * @var CategoryPageEntity $categoryPage
     */
    protected $categoryPage;

    /**
     * @var FrameEntity|Null $frame
     */
    protected $frame;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type' => 'select',
                'name' => 'link_to',
                'label' => 'Link To',
                'options' => array(
                    'options' => $this->createPagesAndLinksArray($this->categoryPage),
                ),
            )
        );

        $this->add(
            array(
                'type' => 'checkbox',
                'name' => 'active',
                'label' => 'Active',
                'value' => true,
            )
        );

        $this->addSubmit('Add', 'frame_add');
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type' => 'textarea',
                'name' => 'description',
                'label' => 'Description',
                'required' => $isDefault,
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    protected function createPagesAndLinksArray(CategoryPageEntity $categoryPage)
    {
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findByCategory($categoryPage->getCategory());

        $links = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Link')
            ->findByCategory($categoryPage->getCategory());

        $pageOptions = array(
            '' => '',
        );
        foreach ($pages as $page) {
            $pageOptions["page_".$page->getId()] = $page->getTitle();
        }
        foreach ($links as $link) {
            $pageOptions["link_".$link->getId()] = $link->getName();
        }

        return $pageOptions;
    }

    /**
     * @param FrameEntity $frame
     * @return self
     */
    public function setFrame(FrameEntity $frame)
    {
        $this->frame = $frame;

        return $this;
    }

    /**
     * @param CategoryPageEntity $page
     * @return self
     */
    public function setCategoryPage(CategoryPageEntity $page)
    {
        $this->categoryPage = $page;

        return $this;
    }
}
