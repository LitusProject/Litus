<?php

namespace CommonBundle\Component\Doctrine\Migrations\Version;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Interop\Container\ContainerInterface;

class MigrationFactoryDecorator implements MigrationFactory
{
    /**
     * @var MigrationFactory
     */
    private $migrationFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(MigrationFactory $migrationFactory, ContainerInterface $container)
    {
        $this->migrationFactory = $migrationFactory;
        $this->container = $container;
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $instance = $this->migrationFactory->createVersion($migrationClassName);

        if ($instance instanceof ContainerAwareInterface) {
            $instance->setContainer($this->container);
        }

        return $instance;
    }
}
