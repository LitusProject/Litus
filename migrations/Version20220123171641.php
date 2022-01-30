<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220123171641
 */
class Version20220123171641 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE br_events_companies_map DROP CONSTRAINT fk_1e92e166dc9ee959');
        $this->addSql('DROP SEQUENCE br_events_companies_attendee_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE br_events_companies_metadata_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE br_events_companies_location_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_events_matches_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_events_visitors_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_events_companies_location (id BIGINT NOT NULL, company_id BIGINT DEFAULT NULL, event_entity BIGINT DEFAULT NULL, number BIGINT NOT NULL, x BIGINT NOT NULL, y BIGINT NOT NULL, orientation TEXT NOT NULL, type TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C2F4A9B4979B1AD6 ON br_events_companies_location (company_id)');
        $this->addSql('CREATE INDEX IDX_C2F4A9B43CB1333A ON br_events_companies_location (event_entity)');
        $this->addSql('CREATE UNIQUE INDEX number_event_unique ON br_events_companies_location (event_entity, number)');
        $this->addSql('CREATE TABLE br_events_matches (id BIGINT NOT NULL, companymap BIGINT DEFAULT NULL, subscription BIGINT DEFAULT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D1D92C66277755D ON br_events_matches (companymap)');
        $this->addSql('CREATE INDEX IDX_6D1D92C6A3C664D3 ON br_events_matches (subscription)');
        $this->addSql('CREATE UNIQUE INDEX map_subscription_unique ON br_events_matches (companymap, subscription)');
        $this->addSql('CREATE TABLE br_events_visitors (id BIGINT NOT NULL, event_id BIGINT DEFAULT NULL, qr_code TEXT NOT NULL, entry_timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, exit_timestamp TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_22ACA2AF71F7E88B ON br_events_visitors (event_id)');
        $this->addSql('ALTER TABLE br_events_companies_location ADD CONSTRAINT FK_C2F4A9B4979B1AD6 FOREIGN KEY (company_id) REFERENCES br_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events_companies_location ADD CONSTRAINT FK_C2F4A9B43CB1333A FOREIGN KEY (event_entity) REFERENCES br_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events_matches ADD CONSTRAINT FK_6D1D92C66277755D FOREIGN KEY (companymap) REFERENCES br_events_companies_map (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events_matches ADD CONSTRAINT FK_6D1D92C6A3C664D3 FOREIGN KEY (subscription) REFERENCES br_events_subscriptions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events_visitors ADD CONSTRAINT FK_22ACA2AF71F7E88B FOREIGN KEY (event_id) REFERENCES br_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE br_events_companies_attendee');
        $this->addSql('DROP TABLE br_events_companies_metadata');
        $this->addSql('ALTER TABLE br_events ADD view_information_nl TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events ADD view_information_en TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events ADD food TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_companies_map DROP CONSTRAINT fk_57361c8c71f7e88b');
        $this->addSql('ALTER TABLE br_events_companies_map DROP CONSTRAINT fk_57361c8c979b1ad6');
        $this->addSql('DROP INDEX idx_1e92e166dc9ee959');
        $this->addSql('ALTER TABLE br_events_companies_map ADD attendees BIGINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE br_events_companies_map ADD masterInterests TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_companies_map ADD notes TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_companies_map ADD checked BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE br_events_companies_map DROP metadata_id');
        $this->addSql('ALTER TABLE br_events_companies_map ADD CONSTRAINT FK_1E92E166979B1AD6 FOREIGN KEY (company_id) REFERENCES br_companies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events_companies_map ADD CONSTRAINT FK_1E92E16671F7E88B FOREIGN KEY (event_id) REFERENCES br_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events_subscriptions DROP CONSTRAINT FK_18F9705871F7E88B');
        $this->addSql('ALTER TABLE br_events_subscriptions ADD other_university TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_subscriptions ADD other_study TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_subscriptions ALTER food DROP NOT NULL');
        $this->addSql('ALTER TABLE br_events_subscriptions ADD CONSTRAINT FK_18F9705871F7E88B FOREIGN KEY (event_id) REFERENCES br_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX event_qr ON br_events_subscriptions (event_id, qr_code)');
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
