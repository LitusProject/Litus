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

namespace SportBundle\Form\Admin\Group;

use LogicException;
use SportBundle\Entity\Group;
/**
 * Edit to edit the boolean of speedygroup
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Editspeedygroup extends \CommonBundle\Component\Form\Admin\Form
{
	
	  /**
     * @var Group|null
     */
    protected $group = null;
    
	public function init(){
		
        parent::init();
        
        
        if (null === $this->group) {
            throw new LogicException('Cannot edit null group');
        }
		$value = $this->group->getIsSpeedyGroup();
		if ($value === null){
			$value = false;
		}
			
       
        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'isSpeedyGroup',
            'label' => 'Is this group a speedy group ?',
            'value' =>$value,
        ));
       
        $this->addSubmit('Edit speedygroup', 'edit');
	}
    
    
      /**
     * @param  Group $group
     * @return self
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

  
}
