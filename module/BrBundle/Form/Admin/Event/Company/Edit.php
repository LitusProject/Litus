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

namespace BrBundle\Form\Admin\Event\Company;


use BrBundle\Entity\Event\CompanyMetadata;

class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Event\CompanyMetadata';
    protected $companyMetadata;

    public function init()
    {
        parent::init();

        $masterFields = array();
        foreach (CompanyMetadata::POSSIBLE_MASTERS as $master_key => $master) {
            $masterFields[] = array(
                'type'       => 'select',
                'name'       => $master_key,
                'label'      => $master,
                'required'   => false,
                'attributes' => array(
                    'id'      => 'study',
                    'options' => $this->getInterestOptions(),
                ),
                'options' => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            );
        }

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'master_interests',
                'label'    => 'Master interests',
                'elements' => $masterFields,
            )
        );

        $this->remove('submit')
            ->addSubmit('Save', 'edit');

        if ($this->companyMetadata !== null) {
            $this->bind($this->companyMetadata);
        }
    }

    public function getInterestOptions() {
        return array(
            'Not interested',
            'Partially interested',
            'Core business'
        );
    }

    /**
     * @param  CompanyMetadata|null $companyMetadata
     * @return self
     */
    public function setCompanyMetadata(CompanyMetadata $companyMetadata)
    {
        $this->companyMetadata = $companyMetadata;

        return $this;
    }
}
