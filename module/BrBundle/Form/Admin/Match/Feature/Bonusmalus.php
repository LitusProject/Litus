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

namespace BrBundle\Form\Admin\Match\Feature;

use BrBundle\Entity\Match\Feature;

/**
 * Add Bonus and Malus Features
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class Bonusmalus extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Feature
     */
    private $feature;

    /**
     * @var array All features
     */
    private $features;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'bonus',
                'label'      => 'Bonus Articles',
                'attributes' => array(
                    'multiple' => true,
                    'style'    => 'max-width: 100%;min-width: 50%;height: 250px;',
                    'options'  => $this->getFeaturesNames(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'malus',
                'label'      => 'Malus Articles',
                'attributes' => array(
                    'multiple' => true,
                    'style'    => 'max-width: 100%;min-width:50%;height: 250px;',
                    'options'  => $this->getFeaturesNames(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('BonusMalus', 'feature_add');

        $this->populateBonusMalus();
    }

    public function setFeature(Feature $feat)
    {
        $this->feature = $feat;

        return $this;
    }

    /**
     * @return array
     */
    private function getFeaturesNames()
    {
        $featNames = array();
        foreach ($this->features as $feat) {
            if ($feat->getName() != $this->feature->getName()){
                $featNames[$feat->getId()] = $feat->getName();
            }
        }

        return $featNames;
    }

    /**
     * @return void
     */
    private function populateBonusMalus()
    {
        if ($this->feature != null){
            $data = array();
            foreach ($this->feature->getBonus() as $b)
                $data['bonus'][] = $b->getId();
            foreach ($this->feature->getMalus() as $b)
                $data['malus'][] = $b->getId();

            $this->populateValues($data, true);
        }
    }

    /**
     * @param  array All possible features
     * @return self
     */
    public function setFeatures(array $features)
    {
        $this->features = $features;

        return $this;
    }
}
