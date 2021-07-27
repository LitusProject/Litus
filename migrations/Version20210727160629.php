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

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210727160629
 */
class Version20210727160629 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE br_match_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_match_feature_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_match_matchee_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_match_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_match_profile_feature_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_match (id BIGINT NOT NULL, company BIGINT DEFAULT NULL, student BIGINT DEFAULT NULL, match_percentage INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CDBA31974FBF094F ON br_match (company)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CDBA3197B723AF33 ON br_match (student)');
        $this->addSql('CREATE TABLE br_match_feature (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE br_match_matchee_map (id BIGINT NOT NULL, companyProfile_id BIGINT DEFAULT NULL, studentProfile_id BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CBB8A49FD203A85 ON br_match_matchee_map (companyProfile_id)');
        $this->addSql('CREATE INDEX IDX_CBB8A49A95571F5 ON br_match_matchee_map (studentProfile_id)');
        $this->addSql('CREATE TABLE br_match_matchee_map_company (id BIGINT NOT NULL, company BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_94BE8AFF4FBF094F ON br_match_matchee_map_company (company)');
        $this->addSql('CREATE TABLE br_match_matchee_map_student (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C222C8334DCD176 ON br_match_matchee_map_student (person)');
        $this->addSql('CREATE TABLE br_match_profile (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE br_match_profile_companyprofile (id BIGINT NOT NULL, company BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB6EED774FBF094F ON br_match_profile_companyprofile (company)');
        $this->addSql('CREATE TABLE br_match_profile_feature_map (id BIGINT NOT NULL, feature BIGINT DEFAULT NULL, profile BIGINT DEFAULT NULL, importance INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A89CA721FD77566 ON br_match_profile_feature_map (feature)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A89CA728157AA0F ON br_match_profile_feature_map (profile)');
        $this->addSql('CREATE TABLE br_match_profile_studentprofile (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_48C7518234DCD176 ON br_match_profile_studentprofile (person)');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA31974FBF094F FOREIGN KEY (company) REFERENCES br_match_matchee_map_company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA3197B723AF33 FOREIGN KEY (student) REFERENCES br_match_matchee_map_student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A49FD203A85 FOREIGN KEY (companyProfile_id) REFERENCES br_match_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A49A95571F5 FOREIGN KEY (studentProfile_id) REFERENCES br_match_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map_company ADD CONSTRAINT FK_94BE8AFF4FBF094F FOREIGN KEY (company) REFERENCES br_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map_student ADD CONSTRAINT FK_6C222C8334DCD176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_companyprofile ADD CONSTRAINT FK_FB6EED774FBF094F FOREIGN KEY (company) REFERENCES br_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_feature_map ADD CONSTRAINT FK_A89CA721FD77566 FOREIGN KEY (feature) REFERENCES br_match_feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_feature_map ADD CONSTRAINT FK_A89CA728157AA0F FOREIGN KEY (profile) REFERENCES br_match_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_studentprofile ADD CONSTRAINT FK_48C7518234DCD176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

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
