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

namespace PageBundle\Form\Admin\Category;

use Doctrine\ORM\EntityManager,
    PageBundle\Entity\Category,
    Zend\Form\Element\Submit;

/**
 * Edit Category
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \PageBundle\Entity\Category $category The category we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Category $category, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'category_edit');
        $this->add($field);

        $this->_populateFromCategory($category);
    }

    private function _populateFromCategory(Category $category)
    {
        $data = array();
        foreach($this->getLanguages() as $language)
            $data['name_' . $language->getAbbrev()] = $category->getName($language, false);

        $data['parent'] = null !== $category->getParent() ? $category->getParent()->getId() : '';

        $this->setData($data);
    }
}
