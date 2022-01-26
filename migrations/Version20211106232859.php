<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20211106232859
 */
class Version20211106232859 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');


        $this->addSql('ALTER TABLE br_match_matchee_map ADD inheritance_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE br_match_profile ALTER inheritance_type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE br_match_profile ALTER inheritance_type DROP DEFAULT');
        $this->addSql('ALTER TABLE br_match_profile ALTER inheritance_type SET NOT NULL');
        $this->addSql('ALTER TABLE br_match_matchee_map_company ADD CONSTRAINT FK_94BE8AFFBF396750 FOREIGN KEY (id) REFERENCES br_match_matchee_map (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map_student ADD CONSTRAINT FK_6C222C83BF396750 FOREIGN KEY (id) REFERENCES br_match_matchee_map (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX profile_feature_map_feature_profile ON br_match_profile_feature_map (feature, profile)');
        $this->addSql('DROP INDEX uniq_94be8aff4fbf094f');
        $this->addSql('CREATE INDEX IDX_94BE8AFF4FBF094F ON br_match_matchee_map_company (company)');
        $this->addSql('DROP INDEX uniq_6c222c8334dcd176');
        $this->addSql('CREATE INDEX IDX_6C222C8334DCD176 ON br_match_matchee_map_student (person)');
        $this->addSql('CREATE TABLE br_match_profile_companyprofile (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE br_match_profile_studentprofile (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE br_match_profile_companyprofile ADD CONSTRAINT FK_FB6EED77BF396750 FOREIGN KEY (id) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_studentprofile ADD CONSTRAINT FK_48C75182BF396750 FOREIGN KEY (id) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events ADD description TEXT DEFAULT NULL');
        $this->addSql('CREATE SEQUENCE br_match_wave_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_match_wave (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE SEQUENCE br_match_companywave_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_match_companywave (id BIGINT NOT NULL, company BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ECDE82454FBF094F ON br_match_companywave (company)');
        $this->addSql('ALTER TABLE br_match_companywave ADD CONSTRAINT FK_ECDE82454FBF094F FOREIGN KEY (company) REFERENCES br_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match ADD wave BIGINT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_CDBA3197DA04AD89 ON br_match (wave)');
        $this->addSql('ALTER TABLE br_match_companywave ADD wave BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_match_companywave ADD CONSTRAINT FK_ECDE8245DA04AD89 FOREIGN KEY (wave) REFERENCES br_match_wave (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_ECDE8245DA04AD89 ON br_match_companywave (wave)');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD match BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A497A5BC505 FOREIGN KEY (match) REFERENCES br_match (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CBB8A497A5BC505 ON br_match_matchee_map (match)');
        $this->addSql('ALTER TABLE br_match_wave ADD creation_time TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE br_match ADD interested BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('CREATE SEQUENCE br_match_companywave_match_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_match_companywave_match_map (id BIGINT NOT NULL, companywave_id BIGINT DEFAULT NULL, match BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3DD8E61850CD60AE ON br_match_companywave_match_map (companywave_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3DD8E6187A5BC505 ON br_match_companywave_match_map (match)');
        $this->addSql('ALTER TABLE br_match_companywave_match_map ADD CONSTRAINT FK_3DD8E61850CD60AE FOREIGN KEY (companywave_id) REFERENCES br_match_companywave (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_companywave_match_map ADD CONSTRAINT FK_3DD8E6187A5BC505 FOREIGN KEY (match) REFERENCES br_match (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA3197DA04AD89 FOREIGN KEY (wave) REFERENCES br_match_companywave_match_map (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE TABLE br_match_feature_bonus_map (bonus1 BIGINT NOT NULL, bonus2 BIGINT NOT NULL, PRIMARY KEY(bonus1, bonus2))');
        $this->addSql('CREATE INDEX IDX_D8ED1FA93393EFEA ON br_match_feature_bonus_map (bonus1)');
        $this->addSql('CREATE INDEX IDX_D8ED1FA9AA9ABE50 ON br_match_feature_bonus_map (bonus2)');
        $this->addSql('CREATE TABLE br_match_feature_malus_map (malus1 BIGINT NOT NULL, malus2 BIGINT NOT NULL, PRIMARY KEY(malus1, malus2))');
        $this->addSql('CREATE INDEX IDX_8541D363D7FC2BD5 ON br_match_feature_malus_map (malus1)');
        $this->addSql('CREATE INDEX IDX_8541D3634EF57A6F ON br_match_feature_malus_map (malus2)');
        $this->addSql('ALTER TABLE br_match_feature_bonus_map ADD CONSTRAINT FK_D8ED1FA93393EFEA FOREIGN KEY (bonus1) REFERENCES br_match_feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_feature_bonus_map ADD CONSTRAINT FK_D8ED1FA9AA9ABE50 FOREIGN KEY (bonus2) REFERENCES br_match_feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_feature_malus_map ADD CONSTRAINT FK_8541D363D7FC2BD5 FOREIGN KEY (malus1) REFERENCES br_match_feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_feature_malus_map ADD CONSTRAINT FK_8541D3634EF57A6F FOREIGN KEY (malus2) REFERENCES br_match_feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_feature ADD type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT FK_CBB8A49FD203A85');
        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT FK_CBB8A49A95571F5');
        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT FK_CBB8A497A5BC505');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A49FD203A85 FOREIGN KEY (companyProfile_id) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A49A95571F5 FOREIGN KEY (studentProfile_id) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A497A5BC505 FOREIGN KEY (match) REFERENCES br_match (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_feature_map DROP CONSTRAINT FK_A89CA721FD77566');
        $this->addSql('ALTER TABLE br_match_profile_feature_map DROP CONSTRAINT FK_A89CA728157AA0F');
        $this->addSql('ALTER TABLE br_match_profile_feature_map ADD CONSTRAINT FK_A89CA721FD77566 FOREIGN KEY (feature) REFERENCES br_match_feature (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_feature_map ADD CONSTRAINT FK_A89CA728157AA0F FOREIGN KEY (profile) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_company_map DROP CONSTRAINT FK_4C170BA94FBF094F');
        $this->addSql('ALTER TABLE br_match_profile_company_map DROP CONSTRAINT FK_4C170BA98157AA0F');
        $this->addSql('ALTER TABLE br_match_profile_company_map ADD CONSTRAINT FK_4C170BA94FBF094F FOREIGN KEY (company) REFERENCES br_companies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_company_map ADD CONSTRAINT FK_4C170BA98157AA0F FOREIGN KEY (profile) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_student_map DROP CONSTRAINT FK_DE998A67B723AF33');
        $this->addSql('ALTER TABLE br_match_profile_student_map DROP CONSTRAINT FK_DE998A678157AA0F');
        $this->addSql('ALTER TABLE br_match_profile_student_map ADD CONSTRAINT FK_DE998A67B723AF33 FOREIGN KEY (student) REFERENCES users_people (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_student_map ADD CONSTRAINT FK_DE998A678157AA0F FOREIGN KEY (profile) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match DROP CONSTRAINT FK_CDBA31974FBF094F');
        $this->addSql('ALTER TABLE br_match DROP CONSTRAINT FK_CDBA3197B723AF33');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA31974FBF094F FOREIGN KEY (company) REFERENCES br_match_matchee_map_company (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA3197B723AF33 FOREIGN KEY (student) REFERENCES br_match_matchee_map_student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_companywave_match_map DROP CONSTRAINT FK_3DD8E61850CD60AE');
        $this->addSql('ALTER TABLE br_match_companywave_match_map DROP CONSTRAINT FK_3DD8E6187A5BC505');
        $this->addSql('ALTER TABLE br_match_companywave_match_map ADD CONSTRAINT FK_3DD8E61850CD60AE FOREIGN KEY (companywave_id) REFERENCES br_match_companywave (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_companywave_match_map ADD CONSTRAINT FK_3DD8E6187A5BC505 FOREIGN KEY (match) REFERENCES br_match (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match DROP CONSTRAINT FK_CDBA3197DA04AD89');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA3197DA04AD89 FOREIGN KEY (wave) REFERENCES br_match_companywave_match_map (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT FK_CBB8A497A5BC505');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A497A5BC505 FOREIGN KEY (match) REFERENCES br_match (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

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
