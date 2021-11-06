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
 * Add Profile
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Match\Profile';

    /**
     * @var array All possible features
     */
    private $features;

    public function init()
    {

        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'type',
                'label'      => 'Company or Student',
                'attributes' => array(
                    'id'      => 'type',
                    'options' => Profile::POSSIBLE_TYPES,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'company',
                'label'      => 'Company',
                'attributes' => array(
                    'multiple' => true,
                    'id'      => 'company_select',
                    'options' => $this->getCompanyArray(),
                ),
                'options' => array(
                    'input' => array(
                        'required' => false,
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'student',
                'label'      => 'Student',
                'attributes' => array(
                    'id'      => 'student_typeahead',
                ),
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                        'required' => false,
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'profile_type',
                'label'      => 'Profile Type',
                'attributes' => array(
                    'id'      => 'type',
                    'options' => Profile::POSSIBLE_TYPES,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'features_ids',
                'label'      => 'Features',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                    'style'    => 'max-width: 100%;max-height: 600px;',
                    'options'  => $this->getFeatureNames(),
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
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
            $featureNames[$feature->getId()] = $feature->getName();
        }

        return $featureNames;
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

    /**
     * @return array
     */
    private function getCompanyArray()
    {
        $companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        $companyArray = array(
            '' => '',
        );
        foreach ($companies as $company) {
            $companyArray[$company->getId()] = $company->getName();
        }

        return $companyArray;
    }
}
