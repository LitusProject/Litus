<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230829110409
 */
class Version20230829110409 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE logistics_article ADD unit BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_article ADD CONSTRAINT FK_88486CA2DCBB0C53 FOREIGN KEY (unit) REFERENCES general_organizations_units (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_88486CA2DCBB0C53 ON logistics_article (unit)');
        $this->addSql('ALTER TABLE logistics_request DROP CONSTRAINT fk_b1e5ed5bc8143fa8');
        $this->addSql('ALTER TABLE logistics_request DROP CONSTRAINT fk_b1e5ed5bcc9be6e5');
        $this->addSql('DROP INDEX idx_b1e5ed5bc8143fa8');
        $this->addSql('DROP INDEX idx_b1e5ed5bcc9be6e5');
        $this->addSql('ALTER TABLE logistics_request DROP referenced_order');
        $this->addSql('ALTER TABLE logistics_request DROP edit_order');
        $this->addSql('ALTER TABLE logistics_request DROP requesttype');
        $this->addSql('ALTER TABLE logistics_request DROP rejectmessage');
        $this->addSql('ALTER TABLE logistics_request DROP flag');
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
