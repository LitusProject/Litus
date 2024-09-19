<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20240307140330
 */
class Version20240307140330 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE br_match_companywave_match_map DROP CONSTRAINT fk_3dd8e61850cd60ae');
        $this->addSql('ALTER TABLE br_match DROP CONSTRAINT fk_cdba3197da04ad89');
        $this->addSql('ALTER TABLE br_match_companywave_match_map DROP CONSTRAINT fk_3dd8e6187a5bc505');
        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT fk_cbb8a497a5bc505');
        $this->addSql('ALTER TABLE br_match DROP CONSTRAINT fk_cdba3197b723af33');
        $this->addSql('ALTER TABLE br_match_profile_company_map DROP CONSTRAINT fk_4c170ba98157aa0f');
        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT fk_cbb8a49a95571f5');
        $this->addSql('ALTER TABLE br_match_matchee_map DROP CONSTRAINT fk_cbb8a49fd203a85');
        $this->addSql('ALTER TABLE br_match_profile_feature_map DROP CONSTRAINT fk_a89ca728157aa0f');
        $this->addSql('ALTER TABLE br_match_profile_student_map DROP CONSTRAINT fk_de998a678157aa0f');
        $this->addSql('ALTER TABLE br_match_profile_companyprofile DROP CONSTRAINT fk_fb6eed77bf396750');
        $this->addSql('ALTER TABLE br_match_profile_studentprofile DROP CONSTRAINT fk_48c75182bf396750');
        $this->addSql('ALTER TABLE br_match_matchee_map_student DROP CONSTRAINT fk_6c222c83bf396750');
        $this->addSql('ALTER TABLE br_match_matchee_map_company DROP CONSTRAINT fk_94be8affbf396750');
        $this->addSql('ALTER TABLE br_match_feature_malus_map DROP CONSTRAINT fk_8541d3634ef57a6f');
        $this->addSql('ALTER TABLE br_match_feature_malus_map DROP CONSTRAINT fk_8541d363d7fc2bd5');
        $this->addSql('ALTER TABLE br_match_profile_feature_map DROP CONSTRAINT fk_a89ca721fd77566');
        $this->addSql('ALTER TABLE br_match_feature_bonus_map DROP CONSTRAINT fk_d8ed1fa93393efea');
        $this->addSql('ALTER TABLE br_match_feature_bonus_map DROP CONSTRAINT fk_d8ed1fa9aa9abe50');
        $this->addSql('ALTER TABLE br_match DROP CONSTRAINT fk_cdba31974fbf094f');
        $this->addSql('ALTER TABLE br_match_companywave DROP CONSTRAINT fk_ecde8245da04ad89');

        $this->addSql('DROP SEQUENCE br_match_companywave_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_match_companywave_match_map_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_match_feature_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_match_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_match_matchee_map_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_match_profile_company_map_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_match_profile_feature_map_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_match_profile_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_match_profile_student_map_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_match_wave_id_seq CASCADE');
        $this->addSql('DROP TABLE br_match_companywave');
        $this->addSql('DROP TABLE br_match_companywave_match_map');
        $this->addSql('DROP TABLE br_match');
        $this->addSql('DROP TABLE br_match_matchee_map_student');
        $this->addSql('DROP TABLE br_match_profile');
        $this->addSql('DROP TABLE br_match_profile_company_map');
        $this->addSql('DROP TABLE br_match_matchee_map');
        $this->addSql('DROP TABLE br_match_feature');
        $this->addSql('DROP TABLE br_match_feature_malus_map');
        $this->addSql('DROP TABLE br_match_matchee_map_company');
        $this->addSql('DROP TABLE br_match_profile_feature_map');
        $this->addSql('DROP TABLE br_match_feature_bonus_map');
        $this->addSql('DROP TABLE br_match_profile_student_map');
        $this->addSql('DROP TABLE br_match_wave');
        $this->addSql('DROP TABLE br_match_profile_companyprofile');
        $this->addSql('DROP TABLE br_match_profile_studentprofile');
        $this->addSql('ALTER TABLE br_companies DROP matching_software_email');
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
