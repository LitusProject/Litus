<?php
declare(strict_types=1);

/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace Migrations;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20190714140846
 */
class Version20190714140846 extends \Doctrine\Migrations\AbstractMigration implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on PostgreSQL.'
        );

        $this->addSql('ALTER TABLE br_invoices_history ADD file VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE br_invoices_manual ADD file VARCHAR(255) DEFAULT \'\' NOT NULL');
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function postUp(Schema $schema) : void
    {
        $config = $this->getServiceLocator()->get('config');
        $filesystem = $this->getServiceLocator()->get('filesystem');
        $directory = $config['litus']['storage']['directories']['br_invoices_manual'];

        $entities = $this->getServiceLocator()
            ->get('doctrine.entitymanager.orm_default')
            ->getRepository('BrBundle\Entity\Invoice\Manual')
            ->findAll();

        foreach ($entities as $entity) {
            $from = $directory
                . '/' . $entity->getInvoiceNumberPrefix()
                . '/' . $entity->getInvoiceNumber() . '.pdf';

            if ($filesystem->has($from)) {
                do {
                    $file = sha1(uniqid());
                    $to = $directory . '/' . $file;
                } while ($filesystem->has($to));

                $entity->setFile($file);
                $filesystem->rename($from, $to);
            }
        }

        $this->getServiceLocator()
            ->get('doctrine.entitymanager.orm_default')
            ->flush();
    }
}
