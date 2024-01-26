<?php

namespace SecretaryBundle\Form\Registration;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\HydratorPluginManagerTrait;
use CommonBundle\Entity\User\Person\Academic;
use Laminas\Validator\Identical;
use SecretaryBundle\Entity\Organization\MetaData;

/**
 * Add Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    use HydratorPluginManagerTrait;

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

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'academic',
                'label'    => 'Personal',
                'elements' => array(
                    array(
                        'type'       => 'text',
                        'name'       => 'first_name',
                        'label'      => 'First Name',
                        'required'   => true,
                        'value'      => $extra['first_name'] ?? '',
                        'attributes' => array(
                            'id' => 'first_name',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'last_name',
                        'label'      => 'Last Name',
                        'required'   => true,
                        'value'      => $extra['last_name'] ?? '',
                        'attributes' => array(
                            'id' => 'last_name',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
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
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'Date',
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
                                'x' => 'X',
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
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'PhoneNumber'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'     => 'fieldset',
                        'name'     => 'university',
                        'elements' => array(
                            array(
                                'type'  => 'hidden',
                                'name'  => 'identification',
                                'value' => $this->identification,
                            ),
                            array(
                                'type'       => 'text',
                                'name'       => 'identification_visible',
                                'label'      => 'University Identification',
                                'value'      => $this->identification,
                                'attributes' => array(
                                    'disabled' => true,
                                ),
                            ),
                            array(
                                'type'       => 'text',
                                'name'       => 'email',
                                'label'      => 'University E-mail',
                                'value'      => $universityEmail,
                                'required'   => true,
                                'attributes' => array(
                                    'id' => 'university_email',
                                ),
                                'options'    => array(
                                    'input' => array(
                                        'filters' => array(
                                            array('name' => 'StringTrim'),
                                        ),
                                        'validators' => array(
                                            array('name' => 'NoAt'),
                                        ),
                                    ),
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
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'EmailAddress'),
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
                        'type'  => 'checkbox',
                        'name'  => 'no_mail',
                        'label' => "I don't want to receive any Corporate Relations mails",
                        'value' => true,
                    ),
                    array(
                        'type'  => 'checkbox',
                        'name'  => 'is_international',
                        'label' => 'I am an international student',
                        'value' => false,
                    ),
                    array(
                        'type'     => 'common_address_add-primary',
                        'name'     => 'primary_address',
                        'label'    => 'Primary Address&mdash;Student Room or Home',
                        'required' => true,
                    ),
                    array(
                        'type'     => 'common_address_add',
                        'name'     => 'secondary_address',
                        'label'    => 'Secondary Address&mdash;Home',
                        'required' => true,
                    ),
                ),
            )
        );

        $registrationEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_registration');

        $memberShipArticles = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $isicMembership = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.isic_membership');

        $this->add(
            array(
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
                        'type'       => count($memberShipArticles) == 0 && $isicMembership == '0' ? 'hidden' : 'checkbox',
                        'name'       => 'become_member',
                        'label'      => 'I want to become a member of the student association in academic year { year } (&euro; { price })',
            //                        'value'      => count($memberShipArticles) != 0 || $isicMembership != '0',
                        'value'      => 1,
                        'attributes' => array(
                            'id'       => 'become_member',
                            'disabled' => $registrationEnabled != 1,
                        ),
                    ),
                    array(
                        'type'       => 'checkbox',
                        'name'       => 'conditions',
                        'label'      => 'I have read and agree with the terms and conditions',
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
                    ),
                    array(
                        'type'       => 'checkbox',
                        'name'       => 'receive_irreeel_at_cudi',
                        'label'      => 'I want to receive my Ir.Reëel at CuDi',
                        'value'      => true,
                        'attributes' => array(
                            'id' => 'irreeel',
                        ),
                    ),
                    array(
                        'type'       => 'checkbox',
                        'name'       => 'bakske_by_mail',
                        'label'      => 'I want to receive \'t Bakske by e-mail',
                        'value'      => true,
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
            )
        );

        $this->addSubmit('Register', 'btn btn-primary', 'register');

        if ($this->metaData !== null) {
            if ($this->metaData->becomeMember()) {
                $organizationInfoFieldset = $this->get('organization_info');
                if ($organizationInfoFieldset->has('organization')) {
                    $organizationInfoFieldset->get('organization')
                        ->setAttribute('disabled', true);
                }

                $organizationInfoFieldset->get('become_member')
                    ->setAttribute('disabled', true);

                $organizationInfoFieldset->get('conditions')
                    ->setValue(true)
                    ->setAttribute('disabled', true);

                $this->conditionsChecked = true;
            }

            $this->bind($this->metaData);
        } elseif ($this->academic !== null) {
            $academicFieldset = $this->get('academic');
            $academicFieldset->populateValues(
                $this->getHydratorPluginManager()
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

        if ($this->isOtherOrganizationEnabled()) {
            $organizationOptions[0] = 'Other';
        }

        foreach ($organizations as $organization) {
            $organizationOptions[$organization->getId()] = $organization->getName();
        }

        return $organizationOptions;
    }

    /**
     * @param  boolean $conditionsChecked
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

        return $this->setAcademic($metaData->getAcademic());
    }

    /**
     * @param  Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic)
    {
        $this->academic = $academic;

        return $this->setIdentification($academic->getUniversityIdentification());
    }

    /**
     * @return boolean
     */
    public function isOtherOrganizationEnabled()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_other_organization');
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();
        if ($this->metaData !== null) {
            if (isset($specs['organization_info']['conditions'])) {
                unset($specs['organization_info']['conditions']);
            }
        }

        return $specs;
    }
}
