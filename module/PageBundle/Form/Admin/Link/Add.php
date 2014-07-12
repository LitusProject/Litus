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
 *
 * @license http://litus.cc/LICENSE
 */

namespace PageBundle\Form\Admin\Link;

use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Entity\General\Language,
    PageBundle\Entity\Category;

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

        $this->add(array(
            'type'       => 'select',
            'name'       => 'category',
            'label'      => 'Category',
            'required'   => true,
            'options'    => array(
                'options' => $this->_createCategoriesArray(),
            ),
        ));

        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        foreach ($categories as $category) {
            $this->add(array(
                'type'       => 'select',
                'name'       => 'parent_' . $category->getId(),
                'label'      => 'Parent',
                'attributes' => array(
                    'class' => 'parent',
                ),
                'options'    => array(
                    'options' => $this->_createPagesArray($category),
                ),
            ));
        }

        $this->addSubmit('Add', 'link_add');
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
            'type'       => 'text',
            'name'       => 'name',
            'label'      => 'Name',
            'required'   => $isDefault,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $container->add(array(
            'type'       => 'text',
            'name'       => 'url',
            'label'      => 'URL',
            'required'   => $isDefault,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'Uri'),
                    ),
                ),
            ),
        ));
    }

    private function _createCategoriesArray()
    {
        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        if (empty($categories))
            throw new \RuntimeException('There needs to be at least one category before you can add a link');

        $categoryOptions = array();
        foreach($categories as $category)
            $categoryOptions[$category->getId()] = $category->getName();

        asort($categoryOptions);

        return $categoryOptions;
    }

    private function _createPagesArray(Category $category)
    {
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findByCategory($category, array('name' => 'ASC'));

        $pageOptions = array(
            '' => ''
        );
        foreach($pages as $page)
            $pageOptions[$page->getId()] = $page->getTitle();

        return $pageOptions;
    }
}
