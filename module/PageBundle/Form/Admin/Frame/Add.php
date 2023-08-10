<?php

namespace PageBundle\Form\Admin\Frame;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;
use PageBundle\Entity\CategoryPage as CategoryPageEntity;
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
     * @var FrameEntity $frame
     */
    protected $frame;

    protected function initBeforeTabs()
    {
        $this->add(
            array(
                'type' => 'hidden',
                'name' => 'category_page_id',
                'attributes' => array(
                    'id' => 'category_page_id',
                ),
            )
        );

        $this->add(
            array(
                'type' => 'radio',
                'name' => 'frame_type',
                'label' => 'Frame Type',
                'required' => true,
                'value' => 'big',
                'attributes' => array(
                    'options' => array(
                        'big' => 'Big Frame',
                        'smalldescription' => 'Small Frame with Description',
                        'smallposter' => 'Small Frame with Poster',
                    ),
                    'class' => 'frame_type',
                ),
            )
        );

        $category_pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\CategoryPage')
            ->findAll();

        foreach ($category_pages as $category_page) {
            $this->add(
                array(
                    'type' => 'select',
                    'name' => 'link_to_' . $category_page->getId(),
                    'label' => 'Link To',
                    'attributes' => array(
                        'class' => 'link_to',
                        'id' => 'link_to_' . $category_page->getId(),
                    ),
                    'options' => array(
                        'options' => $this->createPagesAndLinksArray($category_page),
                    ),
                )
            );
        }

        $this->add(
            array(
                'type' => 'checkbox',
                'name' => 'active',
                'label' => 'Active',
                'value' => true,
            )
        );

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'order_number',
                'label'   => 'Ordering Number',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type' => 'textarea',
                'name' => 'description',
                'label' => 'Description',
                'required' => false,
                'attributes' => array(
                    'class' => 'description'
                ),
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

    protected function initAfterTabs()
    {
        $this->addSubmit('Add', 'frame_add');
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
            $pageOptions["page_" . $page->getId()] = $page->getTitle();
        }
        foreach ($links as $link) {
            $pageOptions["link_" . $link->getId()] = $link->getName();
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
     * @return FrameEntity
     */
    public function getFrame()
    {
        return $this->frame;
    }
}
