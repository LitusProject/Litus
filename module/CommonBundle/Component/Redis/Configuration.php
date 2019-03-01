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

namespace CommonBundle\Component\Redis;

use InvalidArgumentException;
use RuntimeException;
use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Redis Configuration
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Configuration
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var integer
     */
    protected $timeout = null;

    /**
     * @var string
     */
    protected $persistentId;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $libOptions;

    /**
     * @param array|Traversable $config
     * @return self
     */
    public function __construct($config = null)
    {
        if ($config !== null) {
            if (is_array($config)) {
                $this->processArray($config);
            } elseif ($config instanceof Traversable) {
                $this->processArray(ArrayUtils::iteratorToArray($config));
            } else {
                throw new InvalidArgumentException(
                    'Configuration must be an array or implement the ' . Traversable::class .' interface'
                );
            }
        }
    }

    /**
     * @param string $host
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param integer $port
     * @return self
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param integer $timeout
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param string $persistentId
     * @return self
     */
    public function setPersistentId($persistentId)
    {
        $this->persistentId = $persistentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPersistentId()
    {
        return $this->port;
    }

    /**
     * @param string $database
     * @return self
     */
    public function setDatabase($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param string $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param array $libOptions
     * @return self
     */
    public function setLibOptions(array $libOptions)
    {
        $this->libOptions = $libOptions;

        return $this;
    }

    /**
     * @return string
     */
    public function getLibOptions()
    {
        return $this->libOptions;
    }

    /**
     * @param array $config
     */
    protected function processArray(array $config)
    {
        foreach ($config as $key => $value) {
            $setter = implode('', array_map('ucfirst', explode('_', $key)));
            $setter = 'set' . $setter;

            if (!method_exists($this, $setter)) {
                throw new RuntimeException(
                    'The configuration key "' . $key . '" does not have a matching setter "' . $setter . '"'
                );
            }

            $this->{$setter}($value);
        }
    }
}
