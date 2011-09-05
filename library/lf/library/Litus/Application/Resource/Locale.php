<?php

namespace Litus\Application\Resource;

use \Zend\Registry;
use \Zend\Config\Ini as ConfigFile;

use \Zend\Application\Resource\Exception\InitializationException;

use \Zend\Application\Resource\AbstractResource;

class Locale extends AbstractResource {

    public function init()
    {
        $options = $this->getOptions();

        $locale = 'en_GB';
        if(array_key_exists('locale', $options))
            $locale = $options['locale'];

        setlocale(LC_ALL, $locale);
    }

}
