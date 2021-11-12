<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210130142631
 */
class Version20210130142631 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cudi_retail DROP CONSTRAINT fk_e32cc2d934dcd176');
        $this->addSql('DROP INDEX idx_e32cc2d934dcd176');
        $this->addSql('ALTER TABLE cudi_retail RENAME COLUMN person TO owner');
        $this->addSql('ALTER TABLE cudi_retail ADD CONSTRAINT FK_E32CC2D9CF60E67C FOREIGN KEY (owner) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E32CC2D9CF60E67C ON cudi_retail (owner)');
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }
}
