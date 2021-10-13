<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20191017143141
 */
class Version20191017143141 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cudi_isic_cards ALTER haspaid SET DEFAULT \'true\'');
        $this->addSql('CREATE UNIQUE INDEX point_unique ON quiz_points (round, team)');
        $this->addSql('ALTER TABLE users_organizations_metadata DROP irreeel_at_cudi');
        $this->addSql('ALTER TABLE users_organizations_metadata DROP bakske_by_mail');
        $this->addSql('ALTER TABLE users_organizations_metadata DROP tshirt_size');
        $this->addSql('ALTER TABLE shift_shifts ALTER reward SET DEFAULT 0');
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
