<?php

namespace FakBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'   => __NAMESPACE__,
        'directory'   => __DIR__,
        'has_layouts' => true,
    )
);
