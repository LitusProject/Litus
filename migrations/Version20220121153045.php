<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220121153045
 */
class Version20220121153045 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE br_events_matches_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_events_matches (id BIGINT NOT NULL, companymap BIGINT DEFAULT NULL, subscription BIGINT DEFAULT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D1D92C66277755D ON br_events_matches (companymap)');
        $this->addSql('CREATE INDEX IDX_6D1D92C6A3C664D3 ON br_events_matches (subscription)');
        $this->addSql('CREATE UNIQUE INDEX map_subscription_unique ON br_events_matches (companymap, subscription)');
        $this->addSql('ALTER TABLE br_events_matches ADD CONSTRAINT FK_6D1D92C66277755D FOREIGN KEY (companymap) REFERENCES br_events_companies_map (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events_matches ADD CONSTRAINT FK_6D1D92C6A3C664D3 FOREIGN KEY (subscription) REFERENCES br_events_subscriptions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
