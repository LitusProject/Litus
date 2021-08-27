<?php
declare(strict_types=1);

namespace Migrations;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Doctrine\DBAL\Schema\Schema;
use MailBundle\Entity\Message;
use MailBundle\Entity\Message\Attachment;

/**
 * Version 20190422081015
 */
class Version20190422081015 extends \Doctrine\Migrations\AbstractMigration implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    private $documents;

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function preUp(Schema $schema) : void
    {
        $this->abortIf(
            !$this->getServiceLocator()->has('doctrine.configuration.odm_default'),
            'Migration can only be executed when Doctrine supports document mapping.'
        );

        $database = $this->getServiceLocator()
            ->get('doctrine.configuration.odm_default')
            ->getDefaultDB();

        $this->documents = $this->getServiceLocator()
            ->get('doctrine.connection.odm_default')
            ->getMongoClient()
            ->selectCollection($database, 'mailbundle_messages')
            ->find();
    }

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

        $this->addSql('CREATE SEQUENCE mail_messages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mail_messages_attachments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mail_messages (id BIGINT NOT NULL, creation_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, body TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mail_messages_attachments (id BIGINT NOT NULL, message BIGINT DEFAULT NULL, filename VARCHAR(255) NOT NULL, content_type VARCHAR(255) NOT NULL, data BYTEA NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_fa718f20b6bd307f ON mail_messages_attachments (message)');
        $this->addSql('ALTER TABLE mail_messages_attachments ADD CONSTRAINT fk_fa718f20b6bd307f FOREIGN KEY (message) REFERENCES mail_messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function postUp(Schema $schema) : void
    {
        $this->abortIf(
            !$this->getServiceLocator()->has('doctrine.configuration.odm_default'),
            'Migration can only be executed when Doctrine supports document mapping.'
        );

        foreach ($this->documents as $document) {
            $message = new Message(
                $document['type'],
                $document['subject'],
                $document['body']
            );
            $message->setCreationTime($document['creation_time']->toDateTime());

            $this->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default')
                ->persist($message);

            if (!isset($document['attachments'])) {
                continue;
            }

            foreach ($document['attachments'] as $attachment) {
                $attachmentDocument = $this->getServiceLocator()
                    ->get('doctrine.connection.odm_default')
                    ->getMongoClient()
                    ->selectCollection($attachment['$db'], $attachment['$ref'])
                    ->findOne(array('_id' => $attachment['$id']));

                if ($attachmentDocument === null) {
                    continue;
                }

                $attachmentEntity = new Attachment(
                    $message,
                    $attachmentDocument['filename'],
                    $attachmentDocument['content_type'],
                    $attachmentDocument['data']->bin
                );

                $this->getServiceLocator()
                    ->get('doctrine.entitymanager.orm_default')
                    ->persist($attachmentEntity);
            }
        }

        $this->getServiceLocator()
            ->get('doctrine.entitymanager.orm_default')
            ->flush();
    }
}
