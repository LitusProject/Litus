<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230717163242
 */
class Version20230717163242 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE frames_frames ADD link_to_page BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE frames_frames ADD link_to_link BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE frames_frames ADD CONSTRAINT FK_724A790D8E82FD1A FOREIGN KEY (link_to_page) REFERENCES nodes_pages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_frames ADD CONSTRAINT FK_724A790DAC24D2CB FOREIGN KEY (link_to_link) REFERENCES nodes_pages_links (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_724A790D8E82FD1A ON frames_frames (link_to_page)');
        $this->addSql('CREATE INDEX IDX_724A790DAC24D2CB ON frames_frames (link_to_link)');
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
