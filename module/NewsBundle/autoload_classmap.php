<?php
return array(
    'NewsBundle\Module'                             => __DIR__ . '/Module.php',
    'NewsBundle\Controller\Admin\InstallController' => __DIR__ . '/src/Controller/Admin/InstallController.php',
    'NewsBundle\Controller\Admin\NewsController'    => __DIR__ . '/src/Controller/Admin/NewsController.php',
    'NewsBundle\Controller\NewsController'          => __DIR__ . '/src/Controller/NewsController.php',
    'NewsBundle\Entity\Nodes\News'                  => __DIR__ . '/src/Entity/Nodes/News.php',
    'NewsBundle\Entity\Nodes\Translation'           => __DIR__ . '/src/Entity/Nodes/Translation.php',
    'NewsBundle\Form\Admin\News\Add'                => __DIR__ . '/src/Form/Admin/News/Add.php',
    'NewsBundle\Form\Admin\News\Edit'               => __DIR__ . '/src/Form/Admin/News/Edit.php',
    'NewsBundle\Repository\Nodes\News'              => __DIR__ . '/src/Repository/Nodes/News.php',
    'NewsBundle\Repository\Nodes\Translation'       => __DIR__ . '/src/Repository/Nodes/Translation.php',
);