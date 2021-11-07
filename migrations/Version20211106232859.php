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
