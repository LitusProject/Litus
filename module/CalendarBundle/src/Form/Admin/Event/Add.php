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

namespace CalendarBundle\Form\Admin\Event;

use CalendarBundle\Component\Validator\Name as EventNameValidator,
    CalendarBundle\Entity\Nodes\Event,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    DateTime,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add an event.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $tabs = new Tabs('languages');
        $this->add($tabs);

        $tabContent = new TabContent('tab_content');

        foreach($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('title_' . $language->getAbbrev());
            $field->setLabel('Title')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Text('location_' . $language->getAbbrev());
            $field->setLabel('Location')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Textarea('content_' . $language->getAbbrev());
            $field->setLabel('Content')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $tabContent->add($pane);
        }

        $this->add($tabContent);

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setRequired();
        $this->add($field);

        $field = new Text('end_date');
        $field->setLabel('End Date');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'calendar_add');
        $this->add($field);
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            foreach($this->getLanguages() as $language) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'title_' . $language->getAbbrev(),
                            'required' => $language->getAbbrev() == \Locale::getDefault(),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                new EventNameValidator($this->_entityManager, $language),
                            ),
                        )
                    )
                );

                if ($language->getAbbrev() !== \Locale::getDefault())
                    continue;

                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'location_' . $language->getAbbrev(),
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
                            'name'     => 'content_' . $language->getAbbrev(),
                            'required' => true,
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );
            }

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
                        'required' => false,
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
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
