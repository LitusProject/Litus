<?php
declare(strict_types=1);

/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace Migrations;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Doctrine\DBAL\Schema\Schema;
use OnBundle\Entity\Slug;

/**
 * Version 20190418203017
 */
class Version20190418203017 extends \Doctrine\Migrations\AbstractMigration implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    private $documents;

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function preUp(Schema $schema) : void
    {
        $database = $this->getServiceLocator()
            ->get('doctrine.configuration.odm_default')
            ->getDefaultDB();

        $this->documents = $this->getServiceLocator()
            ->get('doctrine.connection.odm_default')
            ->getMongoClient()
            ->selectCollection($database, 'onbundle_slugs')
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

        $this->addSql('CREATE SEQUENCE on_slugs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE on_slugs (id BIGINT NOT NULL, creation_person BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, url TEXT NOT NULL, hits BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_34b0451d5a8b0903 ON on_slugs (creation_person)');
        $this->addSql('CREATE UNIQUE INDEX on_slugs_name ON on_slugs (name)');
        $this->addSql('ALTER TABLE on_slugs ADD CONSTRAINT fk_34b0451d5a8b0903 FOREIGN KEY (creation_person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
        foreach ($this->documents as $document) {
            $creationPerson = null;
            if (isset($document['creationPerson'])) {
                $creationPerson = $this->getServiceLocator()
                    ->get('doctrine.entitymanager.orm_default')
                    ->getRepository('CommonBundle\Entity\User\Person')
                    ->findOneById($document['creationPerson']);
            }

            $slug = new Slug($creationPerson);
            $slug->setName($document['name']);
            $slug->setUrl($document['url']);
            $slug->setHits($document['hits']);

            $this->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default')
                ->persist($slug);
        }

        $this->getServiceLocator()
            ->get('doctrine.entitymanager.orm_default')
            ->flush();
    }
}
