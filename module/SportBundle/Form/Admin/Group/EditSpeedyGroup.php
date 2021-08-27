<?php

namespace SportBundle\Form\Admin\Group;

use SportBundle\Entity\Group;

/**
 * Edit to edit the boolean of speedygroup
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class EditSpeedyGroup extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Group|null
     */
    protected $group = null;

    public function init()
    {
        parent::init();

        $value = $this->group->getIsSpeedyGroup();
        if ($value === null) {
            $value = false;
        }

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'isSpeedyGroup',
                'label' => 'Is this group a speedy group ?',
                'value' => $value,
            )
        );

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
