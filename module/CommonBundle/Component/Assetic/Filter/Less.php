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

namespace CommonBundle\Component\Assetic\Filter;

use RuntimeException;

class Less extends \Assetic\Filter\LessFilter
{
    public function __construct()
    {
        exec('/usr/bin/which node', $output, $returnValue);
        if ($returnValue !== 0) {
            throw new RuntimeException('Failed to locate node');
        }
        $nodeBin = array_pop($output);

        exec('/usr/bin/which npm', $output, $returnValue);
        if ($returnValue !== 0) {
            throw new RuntimeException('Failed to locate npm');
        }
        $npmBin = array_pop($output);

        exec($npmBin . ' ' . 'prefix -g', $output, $returnValue);
        if ($returnValue !== 0) {
            throw new RuntimeException('Could not determine npm prefix');
        }
        $npmPrefix = array_pop($output);

        parent::__construct($nodeBin, array($npmPrefix . '/lib/node_modules'));
        $this->setCompress(true);
    }
}
