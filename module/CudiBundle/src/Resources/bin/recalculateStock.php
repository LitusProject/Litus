<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

/**
 * The script to update the stock
 *
 * Usage:
 * --run|-r      Run the update script
 */
 
chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

// @NOTE: you can either use the git submodule or create an environment variable
// ZF2_PATH that contains the path to your zf2 library (no trailing slash). 
require_once (getenv('ZF2_PATH') ?: 'vendor/ZendFramework/library') . '/Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(
 array('Zend\Loader\StandardAutoloader' => array())
);

$appConfig = include 'config/application.config.php';

$listenerOptions  = new Zend\Module\Listener\ListenerOptions($appConfig['module_listener_options']);
$defaultListeners = new Zend\Module\Listener\DefaultListenerAggregate($listenerOptions);
$defaultListeners->getConfigListener()->addConfigGlobPath('config/autoload/*.config.php');

$moduleManager = new Zend\Module\Manager($appConfig['modules']);
$moduleManager->events()->attachAggregate($defaultListeners);
$moduleManager->loadModules();

$config = $defaultListeners->getConfigListener()->getMergedConfig();

$diContainer = new \Zend\Di\Di();
$diContainer->instanceManager()->addTypePreference('Zend\Di\Locator', $diContainer);
$diConfig = new \Zend\Di\Configuration($config->di);
$diConfig->configure($diContainer);

$em = $diContainer->get('doctrine_em');

$rules = array(
    'run|r' => 'Run the update script',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->r)) {
    echo 'Updating stock...' . PHP_EOL;
    
    $entityManager = $diContainer->get('doctrine_em');
    
    $period = $entityManager
        ->getRepository('CudiBundle\Entity\Stock\Period')
        ->findOneActive();
        
    $period->setEntityManager($entityManager);
        
    $articles = $entityManager
        ->getRepository('CudiBundle\Entity\Stock\Period')
        ->findAllArticlesByPeriod($period);
        
    foreach($articles as $article) {
        $number = $entityManager
                ->getRepository('CudiBundle\Entity\Stock\Periods\Values\Start')
                ->findValueByArticleAndPeriod($article, $period)
            + $period->getNbDelivered($article) - $period->getNbSold($article);
        
        if ($number < 0)
            $number = 0;
        
        if ($article->getStockValue() != $number) {
            echo 'Updated ' . $article->getMainArticle()->getTitle() . ': ' . $article->getStockValue() . ' to ' . $number . PHP_EOL;
            $article->setStockValue($number);
        }
    }
    
    $entityManager->flush();
}
