<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220131125347
 */
class Version20220131125347 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT FK_CBB8A49FD203A85');
        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT FK_CBB8A49A95571F5');
        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT FK_CBB8A497A5BC505');
        $this->addSql('DROP INDEX uniq_cbb8a497a5bc505');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A49FD203A85 FOREIGN KEY (companyProfile_id) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A49A95571F5 FOREIGN KEY (studentProfile_id) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_matchee_map ADD CONSTRAINT FK_CBB8A497A5BC505 FOREIGN KEY (match) REFERENCES br_match (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CBB8A497A5BC505 ON br_match_matchee_map (match)');
        $this->addSql('ALTER TABLE br_match_profile_company_map DROP CONSTRAINT FK_4C170BA94FBF094F');
        $this->addSql('ALTER TABLE br_match_profile_company_map DROP CONSTRAINT FK_4C170BA98157AA0F');
        $this->addSql('ALTER TABLE br_match_profile_company_map ADD CONSTRAINT FK_4C170BA94FBF094F FOREIGN KEY (company) REFERENCES br_companies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_company_map ADD CONSTRAINT FK_4C170BA98157AA0F FOREIGN KEY (profile) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_student_map DROP CONSTRAINT FK_DE998A67B723AF33');
        $this->addSql('ALTER TABLE br_match_profile_student_map DROP CONSTRAINT FK_DE998A678157AA0F');
        $this->addSql('ALTER TABLE br_match_profile_student_map ADD CONSTRAINT FK_DE998A67B723AF33 FOREIGN KEY (student) REFERENCES users_people (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_student_map ADD CONSTRAINT FK_DE998A678157AA0F FOREIGN KEY (profile) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_feature_map DROP CONSTRAINT FK_A89CA721FD77566');
        $this->addSql('ALTER TABLE br_match_profile_feature_map DROP CONSTRAINT FK_A89CA728157AA0F');
        $this->addSql('ALTER TABLE br_match_profile_feature_map ADD CONSTRAINT FK_A89CA721FD77566 FOREIGN KEY (feature) REFERENCES br_match_feature (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_feature_map ADD CONSTRAINT FK_A89CA728157AA0F FOREIGN KEY (profile) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_companywave_match_map DROP CONSTRAINT FK_3DD8E61850CD60AE');
        $this->addSql('ALTER TABLE br_match_companywave_match_map DROP CONSTRAINT FK_3DD8E6187A5BC505');
        $this->addSql('ALTER TABLE br_match_companywave_match_map ADD CONSTRAINT FK_3DD8E61850CD60AE FOREIGN KEY (companywave_id) REFERENCES br_match_companywave (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_companywave_match_map ADD CONSTRAINT FK_3DD8E6187A5BC505 FOREIGN KEY (match) REFERENCES br_match (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_companywave DROP CONSTRAINT FK_ECDE82454FBF094F');
        $this->addSql('ALTER TABLE br_match_companywave DROP CONSTRAINT FK_ECDE8245DA04AD89');
        $this->addSql('ALTER TABLE br_match_companywave ADD CONSTRAINT FK_ECDE82454FBF094F FOREIGN KEY (company) REFERENCES br_companies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_companywave ADD CONSTRAINT FK_ECDE8245DA04AD89 FOREIGN KEY (wave) REFERENCES br_match_wave (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match DROP CONSTRAINT FK_CDBA31974FBF094F');
        $this->addSql('ALTER TABLE br_match DROP CONSTRAINT FK_CDBA3197B723AF33');
        $this->addSql('ALTER TABLE br_match DROP CONSTRAINT FK_CDBA3197DA04AD89');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA31974FBF094F FOREIGN KEY (company) REFERENCES br_match_matchee_map_company (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA3197B723AF33 FOREIGN KEY (student) REFERENCES br_match_matchee_map_student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match ADD CONSTRAINT FK_CDBA3197DA04AD89 FOREIGN KEY (wave) REFERENCES br_match_companywave_match_map (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
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
