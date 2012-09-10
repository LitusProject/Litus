<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Form\Address;

use CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    Doctrine\ORM\EntityManager,
    Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Address
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddPrimary extends \CommonBundle\Component\Form\Bootstrap\Element\Collection
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
     * @var string
     */
    private $_prefix;

    /**
     * @param \Zend\Cache\Storage\StorageInterface $cache The cache instance
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $prefix
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(CacheStorage $cache, EntityManager $entityManager, $prefix = '', $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_cache = $cache;

        $prefix = '' == $prefix ? '' : $prefix . '_';
        $this->_prefix = $prefix;

        list($cities, $streets) = $this->_getCities();

        $field = new Select($prefix . 'address_city');
        $field->setLabel('City')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setAttribute('options', $cities);
        $this->add($field);

        foreach($streets as $id => $collection) {
            $field = new Select($prefix . 'address_street' . $id);
            $field->setLabel('Street')
                ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge ' . $prefix . 'address_street')
                ->setAttribute('options', $collection);
            $this->add($field);
        }

        $field = new Text($prefix . 'address_number');
        $field->setLabel('Number')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired()
            ->setAttribute('size', 5);
        $this->add($field);
    }

    private function _getCities()
    {
        $cacheId = 'Litus_cities_streets';
        if (null !== ($result = $this->_cache->getItem($cacheId))) {
            return $result;
        }

        $cities = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Address\City')
            ->findAll();

        $optionsCity = array(0 => '---');
        $optionsStreet = array();
        foreach($cities as $city) {
            $optionsCity[$city->getId()] = $city->getPostal() . ' ' . $city->getName();
            $optionsStreet[$city->getId()] = array(0 => '---');

            foreach($city->getStreets() as $street) {
                $optionsStreet[$city->getId()][$street->getId()] = $street->getName();
            }
        }

        $this->_cache->setItem($cacheId, array($optionsCity, $optionsStreet));
        return array($optionsCity, $optionsStreet);
    }

    public function getInputs()
    {
        $factory = new InputFactory();
        $inputs = array();

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_city',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'notempty',
                        'options' => array(
                            'type' => 16,
                        ),
                    ),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_street',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'notempty',
                        'options' => array(
                            'type' => 16,
                        ),
                    ),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_number',
                'required' => true,
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

        return $inputs;
    }
}
