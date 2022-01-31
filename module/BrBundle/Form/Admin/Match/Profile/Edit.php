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

namespace BrBundle\Form\Admin\Match\Profile;

use BrBundle\Entity\Match\Profile;

/**
 * Edit Profile
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class Edit extends \BrBundle\Form\Admin\Match\Profile\Add
{
    /**
     * @var Profile
     */
    private $profile;

    public function init()
    {
        parent::init();

        $this->remove('submit')->remove('type')->remove('company')
            ->remove('student')->remove('features')->remove('profile_type');

        // Type features
        foreach ($this->getFeatureNames() as $featureId => $featureName){
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'feature_'.$featureId,
                    'label'      => 'Company Feature: '.$featureName,
                    'value'      => ' ',
                    'attributes' => array(
                        'options'  => $this->makeOptions(),
                    ),
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name'    => 'FeatureImportanceConstraint',
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

        $this
            ->addSubmit('Save Changes', 'feature_edit');

        if ($this->profile !== null) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->profile));
        }
    }


    /**
     * @return array
     */
    private function getFeatureNames()
    {
        $type = $this->profile->getProfileType();
        $featureNames = array();
        foreach ($this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')->findAll() as $feature) {
            if (!$feature->isSector() && ($feature->getType() == 'company' || $feature->getType() == null)) {
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

        $options = array();
        foreach (Profile\ProfileFeatureMap::$POSSIBLE_VISIBILITIES as $val => $type)
            $options[$val] = $type;

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

    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;

        return $this;
    }
}
