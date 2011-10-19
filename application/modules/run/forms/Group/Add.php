<?php

namespace Run\Form\Group;

use \Litus\Application\Resource\Doctrine as DoctrineResource;
use \Litus\Form\Bootstrap\Decorator\ButtonDecorator;
use \Litus\Form\Bootstrap\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Registry;

class Add extends \Litus\Form\Admin\Form
{
    public function __construct(array $allMembers, $options = null)
    {
        parent::__construct($options);

        $field = new Text('group_name');
        $field->setLabel('Group Name')
            ->setAttrib('class', 'xlarge span4')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Select('happy_hour_one');
        $field->setLabel('Happy Hour One')
            ->setAttrib('class', 'xlarge span3')
            ->setRequired()
            ->setMultiOptions($this->_generateHappyHours(20))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Select('happy_hour_two');
        $field->setLabel('Happy Hour Two')
            ->setAttrib('class', 'xlarge span3')
            ->setRequired()
            ->setMultiOptions($this->_generateHappyHours(8))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $this->addDisplayGroup(
            array(
                'group_name',
                'happy_hour_one',
                'happy_hour_two'
            ),
            'group_information'
        );
        $this->getDisplayGroup('group_information')
            ->setLegend('Group Information')
            ->removeDecorator('DtDdWrapper');

        foreach ($allMembers as $memberNb) {
            $this->_generateMemberForm($memberNb);
        }

        $field = new Submit('submit');
        $field->setLabel('Submit')
            ->setAttrib('class', 'btn primary')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

    private function _generateHappyHours($startTime)
    {
        $optionsArray = array();
        for ($i = 0; $i < 6; $i++) {
            $startInterval = ($startTime + 2 * $i) % 24;
            if ($startInterval < 10)
                $startInterval = 0 . $startInterval;

            $endInterval = ($startTime + 2 * ($i + 1)) % 24;
            if ($endInterval < 10)
                $endInterval = 0 . $endInterval;

            $optionKey = $startInterval . $endInterval;
            $optionValue = $startInterval . ':00 - ' . $endInterval . ':00';

            $optionsArray[$optionKey] = $optionValue;
        }

        $entityManager = Registry::get(DoctrineResource::REGISTRY_KEY);
        $groups = $entityManager->getRepository('Litus\Entity\Sport\Group')
            ->findAll();

        $returnArray = $optionsArray;
        foreach ($groups as $group) {
            $happyHours = $group->getHappyHours();

            if (isset($returnArray[$happyHours[0]]))
                unset($returnArray[$happyHours[0]]);

            if (isset($returnArray[$happyHours[1]]))
                unset($returnArray[$happyHours[1]]);
        }

        if (0 == count($returnArray))
            $returnArray = $optionsArray;

        return $returnArray;
    }

    private function _generateMemberForm($memberNb, $required = false)
    {
        $field = new Text('university_identification_' . $memberNb);
        $field->setLabel('Student Number')
            ->setAttrib('class', 'xlarge span2')
            ->setDecorators(array(new FieldDecorator()));

        if ($required)
            $field->setRequired();

        $this->addElement($field);

        $field = new Text('first_name_' . $memberNb);
        $field->setLabel('First Name')
            ->setAttrib('class', 'xlarge span3')
            ->setDecorators(array(new FieldDecorator()));

        if ($required)
            $field->setRequired();

        $this->addElement($field);

        $field = new Text('last_name_' . $memberNb);
        $field->setLabel('Last Name')
            ->setAttrib('class', 'xlarge span3')
            ->setDecorators(array(new FieldDecorator()));

        if ($required)
            $field->setRequired();

        $this->addElement($field);

        $this->addDisplayGroup(
            array(
                'university_identification_' . $memberNb,
                'first_name_' . $memberNb,
                'last_name_' . $memberNb
            ),
            'user_' . $memberNb
        );
        $this->getDisplayGroup('user_' . $memberNb)
            ->setLegend('Team Member ' . ucfirst($memberNb))
            ->removeDecorator('DtDdWrapper');
    }
}