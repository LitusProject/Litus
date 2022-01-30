<?php

namespace BrBundle\Form\Admin\Match\Feature;

use BrBundle\Entity\Match\Feature;

/**
 * Edit Feature
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class Edit extends \BrBundle\Form\Admin\Match\Feature\Add
{
    /**
     * @var Feature
     */
    private $feature;

    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save Changes', 'feature_edit');

        if ($this->feature !== null) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->feature));
        }
    }

    public function setFeature(Feature $feat)
    {
        $this->feature = $feat;

        return $this;
    }
}
