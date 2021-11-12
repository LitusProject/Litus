<?php

namespace BootstrapBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'    => __NAMESPACE__,
        'directory'    => __DIR__,
        'has_entities' => false,
        'has_views'    => false,
    )
);
