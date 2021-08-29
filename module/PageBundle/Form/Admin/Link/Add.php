<?php

namespace PageBundle\Form\Admin\Link;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;
use PageBundle\Entity\Category;
use RuntimeException;

/**
 * Add Link
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'PageBundle\Hydrator\Link';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'category',
                'label'      => 'Category',
                'required'   => true,
                'attributes' => array(
                    'id' => 'category',
                ),
                'options'    => array(
                    'options' => $this->createCategoriesArray(),
                ),
            )
        );

        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        foreach ($categories as $category) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'parent_' . $category->getId(),
                    'label'      => 'Parent',
                    'attributes' => array(
                        'id'    => 'parent_' . $category->getId(),
                        'class' => 'parent',
                    ),
                    'options'    => array(
                        'options' => $this->createPagesArray($category),
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'     => 'select',
                'name'     => 'forced_language',
                'label'    => 'Force Language',
                'required' => true,
                'options'  => array(
                    'options' => $this->createForcedLanguagesArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'active',
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

        $this->addSubmit('Add', 'link_add');
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

        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'url',
                'label'    => 'URL',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Uri'),
                        ),
                    ),
                ),
            )
        );
    }

    private function createCategoriesArray()
    {
        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        if (count($categories) == 0) {
            throw new RuntimeException('There needs to be at least one category before you can add a link');
        }

        $categoryOptions = array();
        foreach ($categories as $category) {
            $categoryOptions[$category->getId()] = $category->getName();
        }

        asort($categoryOptions);

        return $categoryOptions;
    }

    private function createPagesArray(Category $category)
    {
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findByCategory($category, array('name' => 'ASC'));

        $pageOptions = array(
            '' => '',
        );
        foreach ($pages as $page) {
            $pageOptions[$page->getId()] = $page->getTitle();
        }

        return $pageOptions;
    }

    private function createForcedLanguagesArray()
    {
        $languages = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();

        $langArray = array();
        $langArray['None'] = 'None';
        foreach ($languages as $language) {
            $langArray[$language->getAbbrev()] = $language->getName();
        }

        return $langArray;
    }
}
