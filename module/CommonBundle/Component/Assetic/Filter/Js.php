<?php

namespace CommonBundle\Component\Assetic\Filter;

class Js extends \Assetic\Filter\Yui\JsCompressorFilter
{
    public function __construct()
    {
        parent::__construct('vendor/packagelist/yuicompressor-bin/bin/yuicompressor.jar');
    }
}
