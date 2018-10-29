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

namespace PageBundle\Form\Admin\Page;

/**
 * Edit a page.
 */
class Edit extends \PageBundle\Form\Admin\Page\Add
{
    public function init()
    {
        parent::init();

        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        foreach ($categories as $category) {
            /** @var \CommonBundle\Component\Form\Admin\Element\Select $parentField */
            $parentField = $this->get('parent_' . $category->getId());
            $parentField->setValueOptions($this->createPagesArray($category, $this->getPage()->getCategory()->getId() == $category->getId() ? $this->getPage()->getTitle() : ''));
        }

        $this->remove('submit')
            ->addSubmit('Save', 'category_edit');
    }
}
