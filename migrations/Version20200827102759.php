<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20200827102759
 */
class Version20200827102759 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE shift_registration_shifts_registered_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE shift_registration_shifts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE shift_registration_shifts_registered (id BIGINT NOT NULL, signup_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, username VARCHAR(50) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(100) DEFAULT NULL, ticket_code VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE shift_registration_shifts (id BIGINT NOT NULL, creation_person BIGINT DEFAULT NULL, academic_year BIGINT DEFAULT NULL, unit BIGINT DEFAULT NULL, event BIGINT DEFAULT NULL, location BIGINT DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, visible_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, signout_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nb_registered INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, ticket_needed BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D32A58FE5A8B0903 ON shift_registration_shifts (creation_person)');
        $this->addSql('CREATE INDEX IDX_D32A58FE275AE721 ON shift_registration_shifts (academic_year)');
        $this->addSql('CREATE INDEX IDX_D32A58FEDCBB0C53 ON shift_registration_shifts (unit)');
        $this->addSql('CREATE INDEX IDX_D32A58FE3BAE0AA7 ON shift_registration_shifts (event)');
        $this->addSql('CREATE INDEX IDX_D32A58FE5E9E89CB ON shift_registration_shifts (location)');
        $this->addSql('CREATE TABLE shift_registration_shifts_registered_map (registrations_shift BIGINT NOT NULL, registered BIGINT NOT NULL, PRIMARY KEY(registrations_shift, registered))');
        $this->addSql('CREATE INDEX IDX_E971DA77897044DB ON shift_registration_shifts_registered_map (registrations_shift)');
        $this->addSql('CREATE INDEX IDX_E971DA774BFEE160 ON shift_registration_shifts_registered_map (registered)');
        $this->addSql('CREATE TABLE shift_registration_shifts_roles_map (registration_shift BIGINT NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(registration_shift, role))');
        $this->addSql('CREATE INDEX IDX_1C469ACF2AA19BAD ON shift_registration_shifts_roles_map (registration_shift)');
        $this->addSql('CREATE INDEX IDX_1C469ACF57698A6A ON shift_registration_shifts_roles_map (role)');
        $this->addSql('ALTER TABLE shift_registration_shifts ADD CONSTRAINT FK_D32A58FE5A8B0903 FOREIGN KEY (creation_person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shift_registration_shifts ADD CONSTRAINT FK_D32A58FE275AE721 FOREIGN KEY (academic_year) REFERENCES general_academic_years (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shift_registration_shifts ADD CONSTRAINT FK_D32A58FEDCBB0C53 FOREIGN KEY (unit) REFERENCES general_organizations_units (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shift_registration_shifts ADD CONSTRAINT FK_D32A58FE3BAE0AA7 FOREIGN KEY (event) REFERENCES nodes_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shift_registration_shifts ADD CONSTRAINT FK_D32A58FE5E9E89CB FOREIGN KEY (location) REFERENCES general_locations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shift_registration_shifts_registered_map ADD CONSTRAINT FK_E971DA77897044DB FOREIGN KEY (registrations_shift) REFERENCES shift_registration_shifts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shift_registration_shifts_registered_map ADD CONSTRAINT FK_E971DA774BFEE160 FOREIGN KEY (registered) REFERENCES shift_registration_shifts_registered (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shift_registration_shifts_roles_map ADD CONSTRAINT FK_1C469ACF2AA19BAD FOREIGN KEY (registration_shift) REFERENCES shift_registration_shifts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shift_registration_shifts_roles_map ADD CONSTRAINT FK_1C469ACF57698A6A FOREIGN KEY (role) REFERENCES acl_roles (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
