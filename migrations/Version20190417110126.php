<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20190417110126
 */
class Version20190417110126 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE br_companies_requests SET inheritance_type = \'internship\' WHERE inheritance_type = \'internshipRequest\'');
        $this->addSql('UPDATE br_companies_requests SET inheritance_type = \'vacancy\' WHERE inheritance_type = \'vacanyRequest\'');
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
