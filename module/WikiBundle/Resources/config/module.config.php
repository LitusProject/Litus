<?php

namespace WikiBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'    => __NAMESPACE__,
        'directory'    => __DIR__,
        'has_entities' => false,
    )
);
