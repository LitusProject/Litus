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

namespace FormBundle\Form\Admin\Form;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Nodes\Form,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
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

            $field = new TextArea('introduction_' . $language->getAbbrev());
            $field->setLabel('Introduction')
                ->setAttribute('class', 'md')
                ->setAttribute('rows', 20)
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Text('submittext_' . $language->getAbbrev());
            $field->setLabel('Submit Button Text')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $tabContent->add($pane);
        }

        $this->add($tabContent);

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

        $field = new Checkbox('active');
        $field->setLabel('Active');
        $this->add($field);

        $field = new Text('max');
        $field->setLabel('Total Max Entries');
        $this->add($field);

        $field = new Checkbox('multiple');
        $field->setLabel('Multiple Entries / Person Allowed');
        $this->add($field);

        $field = new Checkbox('mail');
        $field->setLabel('Send Confirmation Mail');
        $this->add($field);

        $mail = new Collection('mail_form');
        $mail->setLabel('Mail')
            ->setAttribute('id', 'mail_form');
        $this->add($mail);

        $field = new Text('mail_subject');
        $field->setLabel('Subject')
            ->setRequired();
        $mail->add($field);

        $field = new TextArea('mail_body');
        $field->setLabel('Body')
            ->setAttribute('rows', 20)
            ->setRequired();
        $mail->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'form_add');
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
                        'name'     => 'max',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'digits',
                            ),
                        ),
                    )
                )
            );

            foreach($this->getLanguages() as $language) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'title_' . $language->getAbbrev(),
                            'required' => $language->getAbbrev() == \Locale::getDefault(),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );

                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'introduction_' . $language->getAbbrev(),
                            'required' => $language->getAbbrev() == \Locale::getDefault(),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );

                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'submittext_' . $language->getAbbrev(),
                            'required' => $language->getAbbrev() == \Locale::getDefault(),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );
            }

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
