<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Admin\Company\Job;

use BrBundle\Entity\Company,
    BrBundle\Entity\Company\Job,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a job.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Text('job_name');
        $field->setLabel('Job Name')
            ->setRequired();
        $this->add($field);

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setRequired();
        $this->add($field);

        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setRequired();
        $this->add($field);

        $field = new Select('sector');
        $field->setLabel('Sector')
            ->setAttribute('options', $this->_getSectors());
        $this->add($field);

        $field = new Textarea('description');
        $field->setLabel('Description')
            ->setRequired();
        $this->add($field);

        $field = new Textarea('profile');
        $field->setLabel('Profile')
            ->setRequired();
        $this->add($field);

        $field = new Textarea('requiredKnowledge');
        $field->setLabel('Required Knowledge')
            ->setRequired();
        $this->add($field);

        $field = new Text('city');
        $field->setLabel('City')
            ->setRequired();
        $this->add($field);

        $field = new Select('type');
        $field->setLabel('Type')
            ->setAttribute('options', Job::$possibleTypes)
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'companies_add');
        $this->add($field);
    }

    private function _getSectors()
    {
        $sectorArray = array();
        foreach (Company::$POSSIBLE_SECTORS as $key => $sector)
            $sectorArray[$key] = $sector;

        return $sectorArray;
    }

    public function populateFromJob(Job $job)
    {
        $this->setData(
            array(
                'job_name' => $job->getName(),
                'description' => $job->getDescription(),
                'profile' => $job->getProfile(),
                'requiredKnowledge' => $job->getRequiredKnowledge(),
                'city' => $job->getCity(),
                'start_date' => $job->getStartDate()->format('d/m/Y H:i'),
                'end_date' => $job->getEndDate()->format('d/m/Y H:i'),
                'sector' => $job->getSectorCode(),
            )
        );
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'job_name',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'start_date',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'end_date',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                            new DateCompareValidator('start_date', 'd/m/Y H:i'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'sector',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'description',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'profile',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'requiredKnowledge',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'city',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
