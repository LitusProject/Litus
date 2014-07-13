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

namespace CommonBundle\Component\Hydrator;

use RecursiveArrayIterator,
    RecursiveIteratorIterator,
    RuntimeException,
    Zend\Stdlib\Hydrator\Filter\FilterComposite;

/**
 * A common superclass for hydrators.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Hydrator implements \Zend\Stdlib\Hydrator\HydratorInterface, \CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface
{
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;
    use \CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;

    /**
     * Flattens an array( of arrays)+? of strings into an array of strings
     *
     * @param  string[]|string[][]|array|null $array
     * @return string[]
     */
    private static function flatten(array $array = null)
    {
        if (empty($array)) {
            return array();
        }

        return iterator_to_array(
            new RecursiveIteratorIterator(new RecursiveArrayIterator($array)),
            false
        );
    }

    /**
     * @var string|null The entity class this hydrator extracts/hydrates
     */
    protected $entity = false;

    public function __construct()
    {
        if (false === $this->entity) {
            $entity = get_class($this);
            $entity = explode('\\', $entity, 3);

            if ('Hydrator' != $entity[1])
                throw new RuntimeException('Class ' . get_class($this) . ' should be placed in namespace ' . $entity[0] . '\Hydrator');
            $entity[1] = 'Entity';

            $this->entity = implode('\\', $entity);
        }
    }

    /**
     * Performs the extraction. The parameter has been type checked.
     *
     * @param  object|null $object The object, type checked, or null
     * @return array       The object's data, if any
     */
    abstract protected function doExtract($object = null);

    /**
     * @param  object|null                      $object The object to extract data from, or null
     * @return array                            The object's data, if any
     * @throws Exception\InvalidObjectException If the object is of the wrong class
     */
    public function extract($object = null)
    {
        $this->checkType($object, __METHOD__);

        return $this->doExtract($object);
    }

    /**
     * Performs the hydration. The $object parameter has been typechecked.
     *
     * @param  array       $array  The data, if any
     * @param  object|null $object The object to hydrate
     * @return object      The hydrated $object, or a new instance of $object is null
     */
    abstract protected function doHydrate(array $array, $object = null);

    /**
     * @param  array                            $array  The data, if any
     * @param  object|null                      $object The object to hydrate, or null
     * @return object                           The hydrated object or a new instance if $object is null
     * @throws Exception\InvalidObjectException If the object is of the wrong class
     * @throws \InvalidArgumentException        If the array contains illegal data
     * @throws \InvalidArgumentException        If the object is null and this hydrator cannot create new objects
     */
    public function hydrate(array $array, $object = null)
    {
        $this->checkType($object, __METHOD__);

        return $this->doHydrate($array, $object);
    }

    private function checkType($object = null, $method)
    {
        if (null !== $object && null !== $this->entity) {
            if (!($object instanceof $this->entity)) {
                throw new Exception\InvalidObjectException(
                    sprintf(
                        '%s expects an object of type %s but got %s',
                        $method,
                        $this->entity,
                        is_object($object) ? get_class($object) : gettype($object)
                    )
                );
            }
        }
    }

    /**
     * Hydrates the given $object with the given $data. Only the data values
     * of which the key is given in $keys are hydrated.
     *
     * @param  array                          $data   The data to hydrate
     * @param  object                         $object The object to hydrate
     * @param  string[]|string[][]|array|null $keys   The names of the data values to hydrate
     * @return object
     */
    protected function stdHydrate(array $data, $object, array $keys = null)
    {
        $keys = self::flatten($keys);

        if (empty($keys))
            return $this->getHydrator('classmethods')
                ->hydrate($data, $object);

        return $this->getHydrator('classmethods')
                ->hydrate(array_intersect_key($data, array_flip($keys)), $object);
    }

    /**
     * Extracts data with the given $keys from the given $object.
     *
     * @param  object|null                    $object The object to hydrate
     * @param  string[]|string[][]|array|null $keys   The names of the data values to hydrate
     * @return array
     */
    protected function stdExtract($object = null, array $keys = null)
    {
        if (null === $object)
            return array();

        $keys = self::flatten($keys);

        $hydrator = $this->getHydrator('classmethods');
        if (empty($keys)) {
            $hydrator->addFilter('keys', function ($property) use ($hydrator, $keys, $object) {
                $method = explode('::', $property)[1];

                $attribute = $method;
                if (preg_match('/^get/', $method)) {
                    $attribute = substr($method, 3);
                    if (!property_exists($object, $attribute)) {
                        $attribute = lcfirst($attribute);
                    }
                }

                $attribute = $hydrator->extractName($attribute, $object);

                return in_array($attribute, $keys);
            }, FilterComposite::CONDITION_AND);
        }

        return $hydrator->extract($object);
    }

    /**
     * @param  string                                  $name
     * @return \Zend\Stdlib\Hydrator\HydratorInterface
     */
    public function getHydrator($name)
    {
        return $this->getServiceLocator()
            ->get('litus.hydratormanager')
            ->get($name);
    }

    /**
     * We want an easy method to retrieve the Authentication from
     * the DI container.
     *
     * @return \CommonBundle\Component\Authentication\Authentication
     */
    public function getAuthentication()
    {
        return $this->getServiceLocator()->get('authentication');
    }
}
