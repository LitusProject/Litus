<?php

$terms = array(
    'nl' => getConfigValue($connection, 'secretary.terms_and_conditions_nl'),
    'en' => getConfigValue($connection, 'secretary.terms_and_conditions_en'),
);

addConfigKey($connection, 'secretary.terms_and_conditions', serialize($terms), 'The organization\'\'s terms and conditions');

removeConfigKey($connection, 'secretary.terms_and_conditions_nl');
removeConfigKey($connection, 'secretary.terms_and_conditions_en');