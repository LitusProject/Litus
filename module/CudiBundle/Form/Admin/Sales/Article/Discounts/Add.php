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

namespace CudiBundle\Form\Admin\Sales\Article\Discounts;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Price as PriceValidator,
    CudiBundle\Component\Validator\Sales\Article\Discounts\Exists as DiscountValidator,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Sale\Article\Discount\Discount,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Discount
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var EntityManager
     */
    protected $_entityManager = null;

    /**
     * @var Article
     */
    protected $_article;

    /**
     * @param Article         $article
     * @param EntityManager   $entityManager
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(Article $article, EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_article = $article;

        $field = new Select('template');
        $field->setAttribute('id', 'discount_template')
            ->setLabel('Template')
            ->setAttribute('options', $this->_getTemplates())
            ->setRequired();
        $this->add($field);

        $templates = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Template')
            ->findAll();

        foreach ($templates as $template) {
            $field = new Hidden('template_' . $template->getId() . '_value');
            $field->setValue(number_format($template->getValue()/100, 2));
            $this->add($field);

            $field = new Hidden('template_' . $template->getId() . '_method');
            $field->setValue($template->getMethod());
            $this->add($field);

            $field = new Hidden('template_' . $template->getId() . '_organization');
            $field->setValue($template->getOrganization() ? $template->getOrganization()->getId() : '0');
            $this->add($field);

            $field = new Hidden('template_' . $template->getId() . '_type');
            $field->setValue($template->getType());
            $this->add($field);

            $field = new Hidden('template_' . $template->getId() . '_rounding');
            $field->setValue($template->getRounding());
            $this->add($field);

            $field = new Hidden('template_' . $template->getId() . '_apply_once');
            $field->setValue($template->applyOnce() ? '1' : '0');
            $this->add($field);
        }

        $field = new Text('value');
        $field->setAttribute('id', 'discount_template_value')
            ->setLabel('Value')
            ->setRequired();
        $this->add($field);

        $field = new Select('method');
        $field->setAttribute('id', 'discount_template_method')
            ->setLabel('Method')
            ->setAttribute('options', Discount::$POSSIBLE_METHODS)
            ->setRequired()
            ->setAttribute('data-help', 'The method of this discount:
                <ul>
                    <li><b>Percentage:</b> the value will used as the percentage to substract from the real price</li>
                    <li><b>Fixed:</b> the value will be subtracted from the real price</li>
                    <li><b>Override:</b> the value will be used as the new price</li>
                </ul>');
        $this->add($field);

        $field = new Select('type');
        $field->setAttribute('id', 'discount_template_type')
            ->setLabel('Type')
            ->setRequired()
            ->setAttribute('options', Discount::$POSSIBLE_TYPES);
        $this->add($field);

        $field = new Select('organization');
        $field->setAttribute('id', 'discount_template_organization')
            ->setAttribute('options', $this->_getOrganizations())
            ->setLabel('Organization')
            ->setRequired();
        $this->add($field);

        $field = new Select('rounding');
        $field->setAttribute('id', 'discount_template_rounding')
            ->setLabel('Rounding')
            ->setRequired()
            ->setAttribute('options', $this->_getRoundings());
        $this->add($field);

        $field = new Checkbox('apply_once');
        $field->setAttribute('id', 'discount_template_apply_once')
            ->setLabel('Apply Once')
            ->setAttribute('data-help', 'Enabling this option will allow apply this discount only once to every user.');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'discount_add');
        $this->add($field);
    }

    private function _getTemplates()
    {
        $templates = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Template')
            ->findAll();
        $templateOptions = array(0 => 'none');
        foreach($templates as $template)
            $templateOptions[$template->getId()] = $template->getName();

        return $templateOptions;
    }

    private function _getOrganizations()
    {
        $organizations = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationsOptions = array(0 => 'All');
        foreach($organizations as $organization)
            $organizationsOptions[$organization->getId()] = $organization->getName();

        return $organizationsOptions;
    }

    private function _getRoundings()
    {
        $roundings = array();
        foreach(Discount::$POSSIBLE_ROUNDINGS as $key => $rounding)
            $roundings[$key] = $rounding['name'];

        return $roundings;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'template',
                    'required' => true,
                )
            )
        );

        $required = (isset($this->data['template']) && $this->data['template'] == 0);

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'value',
                    'required' => $required,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator(),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'method',
                    'required' => $required,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'type',
                    'required' => $required,
                    'validators' => array(
                        new DiscountValidator($this->_article, $this->_entityManager),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'organization',
                    'required' => $required,
                )
            )
        );

        return $inputFilter;
    }
}
