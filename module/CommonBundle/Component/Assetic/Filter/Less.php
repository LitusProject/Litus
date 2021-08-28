<?php

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
