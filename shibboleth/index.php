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

/**
 * A little PHP script to enable Shibboleth authentication, when
 * the server's hostname is registered as the IP.
 */

chdir(dirname(__DIR__));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

$shibbolethPersonKey = $em->getRepository('CommonBundle\Entity\General\Config')
    ->getConfigValue('shibboleth_person_key');
$shibbolethSessionKey = $em->getRepository('CommonBundle\Entity\General\Config')
    ->getConfigValue('shibboleth_session_key');

$code = null;
if (isset($_SERVER[$shibbolethPersonKey], $_SERVER[$shibbolethSessionKey])) {
    $code = $em->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
        ->findOneByCode(substr($_SERVER[$shibbolethSessionKey], 1));

    $shibbolethExtraInfoKeys = unserialize(
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_extra_info')
        );
    $extraInfo = array();
    foreach($shibbolethExtraInfoKeys as $key => $value)
        $extraInfo[$key] = isset($_SERVER[$value]) ? $_SERVER[$value] : '';

    if (null === $code) {
        $code = new CommonBundle\Entity\User\Shibboleth\Code(
            $_SERVER[$shibbolethPersonKey],
            substr($_SERVER[$shibbolethSessionKey], 1),
            ($_GET['source'] == 'register') ? 1800 : 300,
            serialize($extraInfo),
            isset($_GET['redirect']) ? $_GET['redirect'] : null
        );

        $em->persist($code);
        $em->flush();
    }
}

$shibbolethHandler = $em->getRepository('CommonBundle\Entity\General\Config')
    ->getConfigValue('shibboleth_code_handler_url');
$shibbolethHandler = unserialize($shibbolethHandler)[$_GET['source']];

if ('/' == substr($shibbolethHandler, -1))
    $shibbolethHandler = substr($shibbolethHandler, 0, -1);

http_response_code(307);
header(
    'Location: ' . $shibbolethHandler . (null !== $code ? '/identification/' . $code->getUniversityIdentification() . '/hash/' . $code->hash() : '')
);
