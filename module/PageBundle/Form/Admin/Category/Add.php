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

namespace PageBundle\Form\Admin\Category;

use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Entity\General\Language;

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

        $this->add(array(
            'type'       => 'select',
            'name'       => 'parent',
            'label'      => 'Parent',
            'options'    => array(
                'options' => $this->_createPagesArray(),
            ),
        ));

        $this->addSubmit('Add', 'category_add');
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
    }

    private function _createPagesArray()
    {
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findAll();

        $pageOptions = array(
            '' => ''
        );
        foreach($pages as $page)
            $pageOptions[$page->getId()] = $page->getTitle();

        return $pageOptions;
    }
}
