<?php

namespace BrBundle\Form\Admin\Match\Wave;

use BrBundle\Entity\Match\Wave;

/**
 * Edit Wave
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class Edit extends \BrBundle\Form\Admin\Match\Wave\Add
{
    /**
     * @var Wave
     */
    private $wave;

    public function init()
    {
        parent::init();

        $this->addSubmit('Save Changes', 'wave_edit');

        if ($this->wave !== null) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->wave));
        }
    }

    public function setWave(Wave $wave)
    {
        $this->wave = $wave;

        return $this;
    }
}
