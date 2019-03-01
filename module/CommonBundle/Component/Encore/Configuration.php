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

namespace CommonBundle\Component\Encore;

use InvalidArgumentException;
use RuntimeException;
use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Encore Configuration
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Configuration
{
    /**
     * @var string
     */
    protected $outputPath;

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
     * @param string $outputPath
     * @return self
     */
    public function setOutputPath($outputPath)
    {
        $this->outputPath = $outputPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutputPath()
    {
        return $this->outputPath;
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
