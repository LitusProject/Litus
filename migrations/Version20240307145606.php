<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20240307145606
 */
class Version20240307145606 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE br_match_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_match (id BIGINT NOT NULL, company BIGINT DEFAULT NULL, academic BIGINT DEFAULT NULL, year BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CDBA31974FBF094F ON br_match (company)');
        $this->addSql('CREATE INDEX IDX_CDBA319740549B08 ON br_match (academic)');
        $this->addSql('CREATE INDEX IDX_CDBA3197BB827337 ON br_match (year)');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA31974FBF094F FOREIGN KEY (company) REFERENCES br_companies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA319740549B08 FOREIGN KEY (academic) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA3197BB827337 FOREIGN KEY (year) REFERENCES general_academic_years (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
