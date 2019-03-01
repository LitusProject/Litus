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

/**
 * Encore
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Encore
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var array
     */
    private $entryPoints;

    /**
     * @param  Configuration $config
     * @return self
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->initEntryPoints();
    }

    /**
     * @param string $entryPoint
     * @return boolean
     */
    public function hasEntryPoint($entryPoint)
    {
        return array_key_exists($entryPoint, $this->entryPoints);
    }

    /**
     * @return void
     */
    public function initEntryPoints()
    {
        $entryPointsJson = $this->config->getOutputPath() . '/entrypoints.json';
        if (!file_exists($entryPointsJson)) {
            throw new RuntimeException('The entrypoints.json file was not found');
        }

        $entryPoints = file_get_contents($entryPointsJson);
        $entryPoints = json_decode($entryPoints, true)['entrypoints'];

        $this->entryPoints = $entryPoints;
    }

    /**
     * @param string $entryPoint
     * @return array
     */
    public function getAssets($entryPoint)
    {
        if (!$this->hasEntryPoint($entryPoint)) {
            throw new InvalidArgumentException(
                'The specified entry point does not exist'
            );
        }

        return $this->entryPoints[$entryPoint];
    }
}
