<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20231114194610
 */
class Version20231114194610 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE shop_reservations_bans ADD sales_session BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE shop_reservations_bans ADD CONSTRAINT FK_197F9CC0DEBDA9B4 FOREIGN KEY (sales_session) REFERENCES shop_sessions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_197F9CC0DEBDA9B4 ON shop_reservations_bans (sales_session)');
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
