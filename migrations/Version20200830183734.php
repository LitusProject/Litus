<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20200830183734
 */
class Version20200830183734 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE registration_shift_users_people_academic_years_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE registration_shift_users_people_academic_years_map (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, academic_year BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DE7A1E7234DCD176 ON registration_shift_users_people_academic_years_map (person)');
        $this->addSql('CREATE INDEX IDX_DE7A1E72275AE721 ON registration_shift_users_people_academic_years_map (academic_year)');
        $this->addSql('CREATE UNIQUE INDEX registration_shift_users_academic_years_map_person_academic_year ON registration_shift_users_people_academic_years_map (person, academic_year)');
        $this->addSql('ALTER TABLE registration_shift_users_people_academic_years_map ADD CONSTRAINT FK_DE7A1E7234DCD176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE registration_shift_users_people_academic_years_map ADD CONSTRAINT FK_DE7A1E72275AE721 FOREIGN KEY (academic_year) REFERENCES general_academic_years (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shift_registration_shifts_registered ADD person BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE shift_registration_shifts_registered ADD CONSTRAINT FK_C68EE20C34DCD176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C68EE20C34DCD176 ON shift_registration_shifts_registered (person)');
        $this->addSql('ALTER TABLE shift_registration_shifts ADD members_visible BOOLEAN DEFAULT \'false\' NOT NULL');
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
