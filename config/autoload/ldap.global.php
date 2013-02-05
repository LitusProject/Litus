<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

if ('production' == getenv('APPLICATION_ENV')) {
    if (!file_exists(__DIR__ . '/../ldap.config.php')) {
        throw new RuntimeException(
            'The LDAP configuration file (' . (__DIR__ . '/../ldap.config.php') . ') was not found'
        );
    }

    return array(
        'service_manager' => array(
            'factories' => array(
                'ldap' => function ($serviceManager) {
                    $ldapConfig = include __DIR__ . '/../ldap.config.php';

                    $ldap = new Zend\Ldap\Ldap(
                        array(
                            'host'           => $ldapConfig['host'],
                            'username'       => $ldapConfig['username'],
                            'password'       => $ldapConfig['password'],
                            'bindRequiresDn' => $ldapConfig['bindRequiresDn'],
                            'baseDn'         => $ldapConfig['baseDn'],
                        )
                    );
                    return $ldap;
                },
            ),
        ),
    );
}

return array();
