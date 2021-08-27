<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20200827125648
 */
class Version20200827125648 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE shift_registration_shifts_registered ADD member BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE shift_registration_shifts ADD members_only BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE shift_registration_shifts ALTER visible_date DROP NOT NULL');
        $this->addSql('ALTER TABLE shift_registration_shifts ALTER signout_date DROP NOT NULL');
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
