<?php

namespace CommonBundle\Component\Assetic\Filter;

class Css extends \Assetic\Filter\Yui\CssCompressorFilter
{
    public function __construct()
    {
        parent::__construct('vendor/packagelist/yuicompressor-bin/bin/yuicompressor.jar');
    }
}
