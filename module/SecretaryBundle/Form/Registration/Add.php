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

namespace SecretaryBundle\Form\Registration;

use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Component\Validator\PhoneNumber as PhoneNumberValidator;
use SecretaryBundle\Component\Validator\NoAt as NoAtValidator;
use SecretaryBundle\Entity\Organization\MetaData;

/**
 * Add Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'SecretaryBundle\Hydrator\Organization\MetaData';

    /**
     * @var boolean Are the conditions already checked or not
     */
    protected $conditionsChecked = false;

    /**
     * @var string
     */
    protected $identification = '';

    /**
     * @var array
     */
    protected $extraInfo = array();

    /**
     * @var MetaData|null
     */
    protected $metaData = null;

    /**
     * @var Academic|null
     */
    protected $academic = null;

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'register_form')
            ->setAttribute('enctype', 'multipart/form-data');

        $extra = $this->extraInfo;

        $universityEmail = '';
        if (isset($extra['email'])) {
            $universityEmail = explode('@', $extra['email'])[0];
        }

        $this->add(array(
            'type'     => 'fieldset',
            'name'     => 'academic',
            'label'    => 'Personal',
            'elements' => array(
                array(
                    'type'     => 'text',
                    'name'     => 'first_name',
                    'label'    => 'First Name',
                    'required' => true,
                    'value'    => isset($extra['first_name']) ? $extra['first_name'] : '',
                    'options'  => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'last_name',
                    'label'    => 'Last Name',
                    'required' => true,
                    'value'    => isset($extra['last_name']) ? $extra['last_name'] : '',
                    'options'  => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'birthday',
                    'label'      => 'Birthday',
                    'required'   => true,
                    'attributes' => array(
                        'placeholder' => 'dd/mm/yyyy',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'Date',
                                    'options' => array(
                                        'format' => 'd/m/Y',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'select',
                    'name'       => 'sex',
                    'label'      => 'Sex',
                    'attributes' => array(
                        'options' => array(
                            'm' => 'M',
                            'f' => 'F',
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'phone_number',
                    'label'      => 'Phone Number',
                    'attributes' => array(
                        'placeholder' => '+CCAAANNNNNN',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                new PhoneNumberValidator(),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'university_identification',
                    'label'      => 'University Identification',
                    'value'      => $this->identification,
                    'attributes' => array(
                        'disabled' => true,
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'university_email',
                    'label'      => 'University E-mail',
                    'value'      => $universityEmail,
                    'required'   => true,
                    'attributes' => array(
                        'id' => 'university_email',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                new NoAtValidator(),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'personal_email',
                    'label'    => 'Personal E-mail',
                    'required' => true,
                    'options'  => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'EmailAddress',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'  => 'checkbox',
                    'name'  => 'primary_email',
                    'label' => 'I want to receive e-mail at my personal e-mail address',
                    'value' => true,
                ),
                array(
                    'type'  => 'common_address_add-primary',
                    'name'  => 'primary_address',
                    'label' => 'Primary Address&mdash;Student Room or Home',
                ),
                array(
                    'type'  => 'common_address_add',
                    'name'  => 'secondary_address',
                    'label' => 'Secondary Address&mdash;Home',
                ),
            ),
        ));

        $registrationEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_registration');

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'organization_info',
            'label'      => 'Student Organization',
            'attributes' => array(
                'id' => 'organization_info',
            ),
            'elements'   => array(
                array(
                    'type'       => 'select',
                    'name'       => 'organization',
                    'label'      => 'Student Organization',
                    'attributes' => array(
                        'id'      => 'organization',
                        'options' => $this->getOrganizations(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'required' => count($this->getOrganizations()) > 1,
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'checkbox',
                    'name'       => 'become_member',
                    'label'      => 'I want to become a member of the student association (&euro; { price })',
                    'value'      => true,
                    'attributes' => array(
                        'id'       => 'become_member',
                        'disabled' => 1 != $registrationEnabled,
                    ),
                ),
                array(
                    'type'       => 'checkbox',
                    'name'       => 'conditions',
                    'label'      => 'I have read and agree with the terms and conditions',
                    'attributes' => array(
                        'id' => 'conditions',
                    ),
                ),
                array(
                    'type'  => 'checkbox',
                    'name'  => 'receive_irreeel_at_cudi',
                    'label' => 'I want to receive my Ir.Reëel at CuDi',
                    'value' => true,
                ),
                array(
                    'type'       => 'checkbox',
                    'name'       => 'bakske_by_mail',
                    'label'      => 'I want to receive \'t Bakske by e-mail',
                    'value'      => false,
                    'attributes' => array(
                        'id' => 'bakske',
                    ),
                ),
                array(
                    'type'       => 'select',
                    'name'       => 'tshirt_size',
                    'label'      => 'T-shirt Size',
                    'attributes' => array(
                        'options' => MetaData::$possibleSizes,
                    ),
                ),
            ),
        ));

        $this->addSubmit('Register', 'btn btn-primary', 'register');

        if (null !== $this->metaData) {
            if ($this->metaData->becomeMember()) {
                if ($this->get('organization_info')->has('organization')) {
                    $this->get('organization_info')->get('organization')
                        ->setAttribute('disabled', true);
                }
                $this->get('organization_info')->get('become_member')
                    ->setAttribute('disabled', true);
                $this->get('organization_info')->get('conditions')
                    ->setValue(true)
                    ->setAttribute('disabled', true);
                $this->conditionsChecked = true;
            }

            $this->bind($this->metaData);
        } elseif (null !== $this->academic) {
            $this->get('academic')->populateValues(
                $this->getServiceLocator()
                    ->get('litus.hydratormanager')
                    ->get('CommonBundle\Hydrator\User\Person\Academic')
                    ->extract($this->academic)
            );
        }
    }

    private function getOrganizations()
    {
        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationOptions = array();

        if ($this->isOtherOrganizationEnabled())
            $organizationOptions[0] = 'Other';

        foreach($organizations as $organization)
            $organizationOptions[$organization->getId()] = $organization->getName();

        return $organizationOptions;
    }

    /**
     * @param  bool $conditionsChecked
     * @return self
     */
    public function setConditionsChecked($conditionsChecked = true)
    {
        $this->conditionsChecked = !!$conditionsChecked;

        return $this;
    }

    /**
     * @param  string $identification
     * @return self
     */
    public function setIdentification($identification)
    {
        $this->identification = $identification;

        return $this;
    }

    /**
     * @param  array $extraInfo
     * @return self
     */
    public function setExtraInfo(array $extraInfo)
    {
        $this->extraInfo = $extraInfo;

        return $this;
    }

    /**
     * @param MetaData
     * @return self
     */
    public function setMetaData(MetaData $metaData)
    {
        $this->metaData = $metaData;

        return $this;
    }

    /**
     * @param  Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOtherOrganizationEnabled()
    {
        return $enableOtherOrganization = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_other_organization');
    }
}
