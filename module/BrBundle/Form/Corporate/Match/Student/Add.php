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

namespace BrBundle\Form\Corporate\Match\Student;

use BrBundle\Entity\Match\Profile;
use Laminas\Validator\Identical;

/**
 * Add Profile
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Match\Profile';

    /**
     * @var array All possible features
     */
    protected $features;

    public function init()
    {

        parent::init();

        foreach ($this->getFeatureNames() as $featureId => $featureName){
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'feature_'.$featureId,
                    'label'      => $featureName,
                    'value'      => ' ',
                    'attributes' => array(
                        'style'   => 'max-height: 38px;height:38px;max-width:150px;',
                        'options' => $this->makeOptions(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'FeatureImportanceConstraint',
                                ),
                            ),
                        ),

                    ),
                )
            );
        }

        // Sector features
        foreach ($this->getSectorFeatureNames() as $featureId => $featureName){
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'sector_feature_'.$featureId,
                    'label'      => 'Sector Feature: '.$featureName,
                    'value'      => 0,
                    'attributes' => array(
                        'style'   => 'max-height: 38px;height:38px;max-width:150px;',
                        'options'  => $this->makeSectorOptions(),
                    ),
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name'    => 'SectorImportanceConstraint',
                                ),
                            ),
                        ),

                    ),
                )
            );
        }

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'conditions',
                'label'      => 'I have read and accept the GDPR terms and condition specified above',
                'attributes' => array(
                    'id' => 'conditions',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'identical',
                                'options' => array(
                                    'token'    => true,
                                    'strict'   => false,
                                    'messages' => array(
                                        Identical::NOT_SAME => 'You must agree to the terms and conditions.',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'profile_add');
    }

    /**

     * @return array
     */
    private function getFeatureNames()
    {
        $featureNames = array();
        foreach ($this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')->findAll() as $feature) {
            if (!$feature->isSector() && ($feature->getType() == 'student' || $feature->getType() == null)){
                $featureNames[$feature->getId()] = $feature->getName();
            }
        }

        return $featureNames;
    }

    /**
     * @return array
     */
    private function makeOptions()
    {

        $options = array(' ');
        foreach (Profile\ProfileFeatureMap::$POSSIBLE_VISIBILITIES as $val => $type) {
            $options[$val] = $type;
        }

        return $options;
    }

    /**
     * @return array
     */
    private function getSectorFeatureNames()
    {
        $featureNames = array();
        foreach ($this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')->findAll() as $feature) {
            if ($feature->isSector())
                $featureNames[$feature->getId()] = $feature->getName();
        }

        return $featureNames;
    }

    /**
     * @return array
     */
    private function makeSectorOptions()
    {
        $amt = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_sector_feature_max_points');
        return range(0,$amt);
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
