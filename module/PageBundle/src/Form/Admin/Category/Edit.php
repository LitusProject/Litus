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

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
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
     * @param mixed $opts The form's options
     */
    public function __construct(EntityManager $entityManager, Category $category, $opts = null)
    {
        parent::__construct($entityManager, $opts);

        $this->removeElement('submit');

        $field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'category_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->_populateFromCategory($category);
    }

    private function _populateFromCategory(Category $category)
    {
        $data = array();
        foreach($this->getLanguages() as $language)
            $data['name_' . $language->getAbbrev()] = $category->getName($language, false);

        $data['parent'] = $category->getParent()->getId();

        $this->populate($data);
    }
}
