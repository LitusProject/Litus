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

namespace SportBundle\Form\Admin\Group;

/**
 * Edit a group of friends.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'typeahead',
            'name'     => 'person',
            'label'    => 'Runner',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        array('name' => 'typeahead_person'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'department',
            'label'      => 'Department',
            'required'   => true,
            'attributes' => array(
                'options' => $this->getDepartments(),
            ),
        ));

        $this->addSubmit('Add Runner', 'product_add');
    }

    private function getDepartments()
    {
        $departments = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Department')
            ->findAll();

        $array = array('0' => '');
        foreach ($departments as $department) {
            $array[$department->getId()] = $department->getName();
        }

        return $array;
    }
}
