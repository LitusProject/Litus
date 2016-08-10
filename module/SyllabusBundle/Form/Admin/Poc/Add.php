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

namespace SyllabusBundle\Form\Admin\Poc;

use SyllabusBundle\Entity\Poc,
	SyllabusBundle\Repository\Group as GroupRepository;


/**
 * Add Poc
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Poc';

    /**
     * @var Group|null
     */
    protected $poc = null;
	
    public function init()
    {	

		parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'person',
            'label'      => 'POC\'er',
            'required'   => true,
            'attributes' => array(
                'id'           => 'person',
                'style'        => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'validators'  => array(
                        array('name' => 'typeahead_person'),
                    ),
                ),
            ),
        ));

		$this->add(array(
            'type'       => 'select',
            'name'       => 'poc_group',
            'label'      => 'Poc Group',
            'required'   => true,
            'attributes' => array(
                'multiple'  => true,
                'options'   => $this->createPocsArray(),
                ),
            )
        );	
  
      

        $this->addSubmit('Add', 'user_add');
    }

    /**
     * @param  Group $group
     * @return self
     */
    public function setGroup(Poc $poc)
    {
        $this->poc = $poc;

        return $this;
    }
    
       /**
     * Returns an array that has all the Poc groups, so that they are available in the
     * poc group multiselect.
     *
     * @return array
     */
    protected function createPocsArray($system = false)
    {
        $pocGroups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAllPocGroupsQuery()->getResult();

        $pocGroupArray = array();
        foreach ($pocGroups as $pocGroup) {
            $pocGroupArray[$pocGroup->getName()] = $pocGroup->getName();
        }

        return $pocGroupArray;
    }
    
}
