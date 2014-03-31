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

namespace CommonBundle\Form\Admin\Address;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\NotZero as NotZeroValidator,
    Doctrine\ORM\EntityManager,
    Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Address
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddPrimary extends \CommonBundle\Component\Form\Admin\Element\Collection
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var \Zend\Cache\Storage\StorageInterface The cache instance
     */
    protected $_cache = null;

    /**
     * @var string The form's prefix
     */
    private $_prefix;

    /**
     * @var boolean Whether or not the form is required
     */
    private $_required;

    /**
     * @param \Zend\Cache\Storage\StorageInterface $cache         The cache instance
     * @param \Doctrine\ORM\EntityManager          $entityManager The EntityManager instance
     * @param string                               $prefix
     * @param string                               $name          Optional name for the element
     * @param boolean                              $required      Whether or not the form is required
     */
    public function __construct(CacheStorage $cache, EntityManager $entityManager, $prefix = '', $name = null, $required = true)
    {
        parent::__construct($name);

        $this->_cache = $cache;
        $this->_entityManager = $entityManager;

        $prefix = '' == $prefix ? '' : $prefix . '_';
        $this->_prefix = $prefix;
        $this->_required = $required;

        list($cities, $streets) = $this->_getCities();

        $field = new Select($prefix . 'address_city');
        $field->setLabel('City')
            ->setAttribute('options', $cities)
            ->setRequired($this->_required);
        $this->add($field);

        $field = new Text($prefix . 'address_postal_other');
        $field->setLabel('Postal Code')
            ->setRequired($this->_required);
        $this->add($field);

        $field = new Text($prefix . 'address_city_other');
        $field->setLabel('City')
            ->setRequired($this->_required);
        $this->add($field);

        $field = new Text($prefix . 'address_street_other');
        $field->setLabel('Street')
            ->setRequired($this->_required);
        $this->add($field);

        foreach ($streets as $id => $collection) {
            $field = new Select($prefix . 'address_street_' . $id);
            $field->setLabel('Street')
                ->setAttribute('class', $field->getAttribute('class') . ' ' . $prefix . 'address_street')
                ->setAttribute('options', $collection);
            $this->add($field);
        }

        $field = new Text($prefix . 'address_number');
        $field->setLabel('Number')
            ->setRequired($this->_required);
        $this->add($field);

        $field = new Text($prefix . 'address_mailbox');
        $field->setLabel('Mailbox');
        $this->add($field);
    }

    private function _getCities()
    {
        if (null !== $this->_cache) {
            if (null !== ($result = $this->_cache->getItem('Litus_CommonBundle_Entity_General_Address_Cities_Streets')))
                return $result;
        }

        $cities = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Address\City')
            ->findAll();

        $optionsCity = array('' => '');
        $optionsStreet = array();
        foreach ($cities as $city) {
            $optionsCity[$city->getId()] = $city->getPostal() . ' ' . $city->getName();
            $optionsStreet[$city->getId()] = array(0 => '');

            foreach ($city->getStreets() as $street) {
                $optionsStreet[$city->getId()][$street->getId()] = $street->getName();
            }
        }
        $optionsCity['other'] = 'Other';

        if (null !== $this->_cache) {
            $this->_cache->setItem(
                'Litus_CommonBundle_Entity_General_Address_Cities_Streets',
                array(
                    $optionsCity,
                    $optionsStreet
                )
            );
        }

        return array($optionsCity, $optionsStreet);
    }

    public function getInputs()
    {
        $factory = new InputFactory();
        $inputs = array();

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_city',
                'required' => $this->_required,
            )
        );

        if ($this->get($this->_prefix . 'address_city')->getValue() != 'other') {
            $inputs[] = $factory->createInput(
                array(
                    'name'     => $this->_prefix . 'address_street_' . $this->get($this->_prefix . 'address_city')->getValue(),
                    'required' => $this->_required,
                )
            );
        } else {
            $inputs[] = $factory->createInput(
                array(
                    'name'     => $this->_prefix . 'address_street_other',
                    'required' => $this->_required,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            );

            $inputs[] = $factory->createInput(
                array(
                    'name'     => $this->_prefix . 'address_postal_other',
                    'required' => $this->_required,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'alnum',
                            'options' => array(
                                'allowWhiteSpace' => true,
                            ),
                        ),
                    ),
                )
            );

            $inputs[] = $factory->createInput(
                array(
                    'name'     => $this->_prefix . 'address_city_other',
                    'required' => $this->_required,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'alpha',
                            'options' => array(
                                'allowWhiteSpace' => true,
                            ),
                        ),
                    ),
                )
            );
        }

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_number',
                'required' => $this->_required,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'alnum',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        ),
                    ),
                    new NotZeroValidator(),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_mailbox',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            )
        );

        return $inputs;
    }
}
