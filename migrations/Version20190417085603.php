<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20190417085603
 */
class Version20190417085603 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on PostgreSQL.'
        );

        $this->addSql('ALTER TABLE cudi_sale_bookings RENAME COLUMN assignmentdate TO assignment_date');
        $this->addSql('ALTER TABLE cudi_sale_bookings RENAME COLUMN bookdate TO book_date');
        $this->addSql('ALTER TABLE cudi_sale_bookings RENAME COLUMN cancelationdate TO cancelation_date');
        $this->addSql('ALTER TABLE cudi_sale_bookings RENAME COLUMN expirationdate TO expiration_date');
        $this->addSql('ALTER TABLE cudi_sale_bookings RENAME COLUMN returndate TO return_date');
        $this->addSql('ALTER TABLE cudi_sale_bookings RENAME COLUMN saledate TO sale_date');

        $this->addSql('DROP INDEX cudi_sale_bookings_book_date');
        $this->addSql('CREATE INDEX cudi_sale_bookings_book_date ON cudi_sale_bookings (book_date)');
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
