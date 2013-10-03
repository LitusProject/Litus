<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace FormBundle\Form\Admin\Field;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    CommonBundle\Component\Form\Admin\Element\Text,
    FormBundle\Component\Validator\Required as RequiredValidator,
    FormBundle\Component\Validator\StringField as StringFieldValidator,
    FormBundle\Entity\Field\Checkbox as CheckboxField,
    FormBundle\Entity\Field\String as StringField,
    FormBundle\Entity\Field\Dropdown as DropdownField,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Field,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Field
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_entityManager = null;

    /**
     * @var \CudiBundle\Entity\Sale\Article
     */
    protected $_form;

    /**
     * @param \CudiBundle\Entity\Sale\Form $form
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Form $form, EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_form = $form;

        $tabs = new Tabs('languages');
        $this->add($tabs);

        $tabContent = new TabContent('tab_content');

        foreach($this->getLanguages() as $language) {

            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('label_' . $language->getAbbrev());
            $field->setLabel('Label')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $dropdown_form = new Collection('dropdown_form_' . $language->getAbbrev());
            $dropdown_form->setLabel('Options')
                ->setAttribute('class', 'dropdown_form extra_form hide');
            $pane->add($dropdown_form);

            $field = new Text('options_' . $language->getAbbrev());
            $field->setLabel('Options');
            $dropdown_form->add($field);

            $tabContent->add($pane);
        }

        $this->add($tabContent);

        $field = new Select('type');
        $field->setLabel('Type')
            ->setRequired()
            ->setAttribute('options', Field::$POSSIBLE_TYPES);
        $this->add($field);

        $field = new Text('order');
        $field->setLabel('Order')
            ->setRequired(True);
        $this->add($field);

        $field = new Checkbox('required');
        $field->setLabel('Required');
        $this->add($field);

        $string_form = new Collection('string_form');
        $string_form->setLabel('String Options')
            ->setAttribute('class', 'string_form extra_form hide');
        $this->add($string_form);

        $field = new Checkbox('multiline');
        $field->setLabel('Multiline');
        $string_form->add($field);

        $field = new Text('charsperline');
        $field->setLabel('Max. characters per line (or Infinite)');
        $string_form->add($field);

        $field = new Text('lines');
        $field->setLabel('Max. number of lines (Multiline fields only)');
        $string_form->add($field);

        $string_form = new Collection('file_form');
        $string_form->setLabel('File Options')
            ->setAttribute('class', 'file_form extra_form hide');
        $this->add($string_form);

        $field = new Text('max_size');
        $field->setLabel('Max. size (in MB)')
            ->setValue(4);
        $string_form->add($field);

        $visibility = new Collection('visibility');
        $visibility->setLabel('Visibility');
        $this->add($visibility);

        $field = new Select('visible_if');
        $field->setLabel('Visible If')
            ->setRequired()
            ->setAttribute('options', $this->_getVisibilityOptions());
        $visibility->add($field);

        $field = new Select('visible_value');
        $field->setLabel('Is')
            ->setRequired();
        $visibility->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'field_add');
        $this->add($field);
    }

    private function _getVisibilityOptions()
    {
        $options = array(0 => 'Always');
        foreach($this->_form->getFields() as $field) {
            if ($field instanceof StringField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'string',
                    )
                );
            } else if ($field instanceof DropdownField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'dropdown',
                        'data-values' => $field->getOptions(),
                    )
                );
            } elseif ($field instanceof CheckboxField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'checkbox',
                    )
                );
            } elseif ($field instanceof FileField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'file',
                    )
                );
            }
        }
        return $options;
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach($this->getLanguages() as $language) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'label_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
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
                    'name'     => 'charsperline',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits'
                        ),
                        new StringFieldValidator(
                            isset($this->data['multiline']) ? $this->data['multiline'] : null,
                            isset($this->data['lines']) ? $this->data['lines'] : null
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'lines',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits'
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'max_size',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits'
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'order',
                    'required' => true,
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

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'required',
                    'required' => false,
                    'validators' => array(
                        new RequiredValidator(),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
