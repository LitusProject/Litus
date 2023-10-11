<?php

namespace PageBundle\Form\Admin\Category;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;

/**
 * Add Category
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'PageBundle\Hydrator\Category';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'parent',
                'label'      => 'Parent',
                'attributes' => array(
                    'id' => 'parent',
                ),
                'options'    => array(
                    'options' => $this->createPagesArray(),
                ),
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

        $this->addSubmit('Add', 'category_add');
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    private function createPagesArray()
    {
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findAll();

        $pageOptions = array(
            '' => '',
        );
        foreach ($pages as $page) {
            $pageOptions[$page->getId()] = $page->getTitle();
        }

        return $pageOptions;
    }
}
