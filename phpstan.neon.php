<?php declare(strict_types = 1);

return array(
    'parameters' => array(
        'tmpDir' => getenv("PHPSTAN_TMP_DIR", true) ?: "",
    ),
);
