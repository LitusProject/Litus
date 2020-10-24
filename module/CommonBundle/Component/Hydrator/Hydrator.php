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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Hydrator;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\AuthenticationTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\HydratorPluginManagerTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use CommonBundle\Component\Util\AcademicYear;
use DateTime;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\HydratorInterface;

/**
 * A common superclass for hydrators.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Hydrator implements HydratorInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use AuthenticationTrait;
    use DoctrineTrait;
    use HydratorPluginManagerTrait;

    /**
     * Flattens an array( of arrays)+? of strings into an array of strings
     *
     * @param  string[]|string[][]|array|null $array
     * @return string[]
     */
    private static function flatten(array $array = null)
    {
        if (count($array) == 0) {
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
        if ($this->entity === false) {
            $entity = static::class;
            $entity = explode('\\', $entity, 3);

            if ($entity[1] != 'Hydrator') {
                throw new RuntimeException('Class ' . static::class . ' should be placed in namespace ' . $entity[0] . '\Hydrator');
            }
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
     * @param  object|null $object The object to extract data from, or null
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
     * @param  array       $array  The data, if any
     * @param  object|null $object The object to hydrate, or null
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

    /**
     * @param mixed|null $object
     * @param string     $method
     */
    private function checkType($object = null, $method = '')
    {
        if ($object !== null && $this->entity !== null) {
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
        $hydrator = $this->getHydrator('classmethods');

        $keys = self::flatten($keys);
        if (count($keys) == 0) {
            return $hydrator->hydrate($data, $object);
        }

        return $hydrator->hydrate(
            array_intersect_key(
                $data,
                array_flip($keys)
            ),
            $object
        );
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
        if ($object === null) {
            return array();
        }

        static $originalHydrator = null;

        if ($originalHydrator === null) {
            $originalHydrator = $this->getHydrator('classmethods');
            $originalHydrator->setNamingStrategy(new NamingStrategy\RemoveIs());
        }

        $hydrator = clone $originalHydrator;

        $keys = self::flatten($keys);
        if (count($keys) > 0) {
            $hydrator->addFilter(
                'keys',
                function ($property) use ($hydrator, $keys, $object) {
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
                },
                FilterComposite::CONDITION_AND
            );
        }

        return $hydrator->extract($object);
    }

    /**
     * Get the current academic year.
     *
     * @param  boolean $organization
     * @return AcademicYear
     */
    public function getCurrentAcademicYear($organization = false)
    {
        if ($organization) {
            return AcademicYear::getOrganizationYear($this->getEntityManager());
        }

        return AcademicYear::getUniversityYear($this->getEntityManager());
    }

    /**
     * @param  string $name
     * @return HydratorInterface
     */
    public function getHydrator($name)
    {
        return $this->getHydratorPluginManager()->get($name);
    }

    /**
     * Returns the logged in user.
     *
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPersonEntity()
    {
        return $this->getAuthentication()->getPersonObject();
    }

    /**
     * Loads the given date, ignoring the time.
     * @param  string $date
     * @return DateTime|null
     */
    protected static function loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y', $date) ?: null;
    }

    /**
     * Loads the given date and time.
     * @param  string $date
     * @return DateTime|null
     */
    protected static function loadDateTime($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }

    /**
     * Loads the given time, ignoring the date.
     * @param  string $date
     * @return DateTime|null
     */
    protected static function loadTime($date)
    {
        return DateTime::createFromFormat('H#i', $date) ?: null;
    }

    protected function getLanguages()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }
}
