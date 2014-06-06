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

namespace BrBundle\Form\Admin\Product;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\Price as PriceValidator,
    BrBundle\Entity\Product,
    BrBundle\Component\Validator\ProductName as ProductNameValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a product.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed                       $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

        $field = new Textarea('description');
        $field->setLabel('Description')
            ->setRequired();
        $this->add($field);

        $field = new Text('price');
        $field->setLabel('Price')
            ->setRequired()
            ->setValue('0');
        $this->add($field);

        $field = new Select('vat_type');
        $field->setLabel('VAT Type')
            ->setRequired()
            ->setAttribute('options', $this->_getVatTypes($entityManager));
        $this->add($field);

        $field = new Text('invoice_description');
        $field->setLabel('Invoice Text')
            ->setRequired(false);
        $this->add($field);

        $field = new Textarea('contract_text');
        $field->setLabel('Contract Text')
            ->setRequired(false)
            ->setValue('<entry>
A single entry is a single bullet on the contract. Formatting options are indicated on the right and entries can be nested by including an "entries" tag in a parent entry, like so:
    <entries>
        <entry>
            This is a nested entry.
        </entry>
    </entries>
</entry>');
        $this->add($field);

        $field = new Select('event');
        $field->setLabel('Event')
            ->setAttribute('disable', array(2292))
            ->setValueOptions($this->_createEventsArray());
        $this->add($field);

        $field = new Text('delivery_date');
        $field->setLabel('Delivery Date')
            ->setRequired(true)
            ->setAttribute('placeholder', 'dd/mm/yyyy')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium start');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'product_add');
        $this->add($field);
    }

    private function _createEventsArray()
    {
        $events = $this->_entityManager
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $eventsArray = array(
            array(
                'label' => '',
                'value' => '',
            )
        );
        foreach ($events as $event) {
            $eventsArray[] = array(
                'label' => $event->getTitle(),
                'value' => $event->getId(),
                'attributes' => array(
                    'data-date' => $event->getStartDate()->format('d/m/Y')
                ),
            );
        }

        return $eventsArray;
    }

    public function populateFromProduct(Product $product)
    {
        $formData = array(
            'name'  => $product->getName(),
            'description' => $product->getDescription(),
            'price' => number_format($product->getPrice()/100, 2),
            'vat_type' => $product->getVatType(),
            'invoice_description' => $product->getInvoiceDescription(),
            'contract_text' => $product->getContractText(),
            'event' => null === $product->getEvent() ? '' : $product->getEvent()->getId(),
            'delivery_date' => null === $product->getDeliveryDate() ? '' : $product->getDeliveryDate()->format('d/m/Y')
        );

        $this->setData($formData);
    }

    /**
     * Retrieve the different VAT types applicable.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     */
    private function _getVatTypes(EntityManager $entityManager)
    {
        $types =  $entityManager->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.vat_types');
        $types = unserialize($types);
        $typesArray = array();
        foreach ($types as $type => $value)
            $typesArray[$type] = $value . '%';

        return $typesArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new ProductNameValidator($this->_entityManager),
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
                    )
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'price',
                    'required' => true,
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
                    'name'     => 'invoice_description',
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
                    'name'     => 'contract_text',
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
                    'name'     => 'delivery_date',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
