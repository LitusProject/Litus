<?php

namespace Litus\Application\Resource;

use \Zend\Registry;
use \Zend\Config\Ini as ConfigFile;

use \Zend\Application\Resource\Exception\InitializationException;

class Config extends \Zend\Application\Resource\AbstractResource {

    public function init()
    {
        $options = $this->getOptions();

        if(!array_key_exists('config', $options))
            throw new InitializationException(
                'The configuration is incomplete; please define resources.litus.config in application.ini'
            );

        $environment = APPLICATION_ENV;
        if(array_key_exists('environment', $options))
            $environment = $options['environment'];

        $options = new ConfigFile($options['config'], $environment);
        $options = $options->toArray();

        foreach ($options as $key => $value) {
            Registry::set('litus.' . $key, $value);
        }
    }

}
