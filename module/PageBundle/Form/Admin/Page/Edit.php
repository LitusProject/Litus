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

namespace PageBundle\Form\Admin\Page;

use PageBundle\Component\Validator\Title as TitleValidator;

/**
 * Edit a page.
 */
class Edit extends Add
{
    public function init()
    {
        parent::init();

        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        foreach ($categories as $category) {
            $this->get('parent_' . $category->getId())
                ->setValueOptions($this->createPagesArray($category, $this->getPage()->getCategory()->getId() == $category->getId() ? $this->getPage()->getTitle() : ''));
        }

        $this->remove('submit');

        $this->addSubmit('Save', 'category_edit');
    }
}
