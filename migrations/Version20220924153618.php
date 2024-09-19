<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220924153618
 */
class Version20220924153618 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users_preferences ALTER section TYPE BIGINT USING section::bigint');
        $this->addSql('ALTER TABLE users_preferences ALTER section DROP DEFAULT');
        $this->addSql('ALTER TABLE users_preferences ALTER section DROP NOT NULL');
        $this->addSql('ALTER TABLE users_preferences ADD CONSTRAINT FK_1E849A072D737AEF FOREIGN KEY (section) REFERENCES mail_sections (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1E849A072D737AEF ON users_preferences (section)');
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
