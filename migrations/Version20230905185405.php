<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230905185405
 */
class Version20230905185405 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE shop_sessions_messages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE shop_sessions_messages_translations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
       $this->addSql('CREATE TABLE shop_sessions_messages (id BIGINT NOT NULL, creation_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE shop_sessions_messages_translations (id BIGINT NOT NULL, message BIGINT DEFAULT NULL, language BIGINT DEFAULT NULL, content VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D38ABBADB6BD307F ON shop_sessions_messages_translations (message)');
        $this->addSql('CREATE INDEX IDX_D38ABBADD4DB71B5 ON shop_sessions_messages_translations (language)');
       $this->addSql('ALTER TABLE shop_sessions_messages_translations ADD CONSTRAINT FK_D38ABBADB6BD307F FOREIGN KEY (message) REFERENCES shop_sessions_messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_sessions_messages_translations ADD CONSTRAINT FK_D38ABBADD4DB71B5 FOREIGN KEY (language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
