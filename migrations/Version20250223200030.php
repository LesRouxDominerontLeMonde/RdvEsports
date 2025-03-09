<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250223200030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create villes table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE villes (
            code_insee INT PRIMARY KEY,
            nom VARCHAR(255) DEFAULT NULL,
            code_postal INT DEFAULT NULL,
            nom_commune VARCHAR(255) DEFAULT NULL,
            latitude DOUBLE DEFAULT NULL,
            longitude DOUBLE DEFAULT NULL,
            departement VARCHAR(255) DEFAULT NULL,
            code_departement INT DEFAULT NULL,
            region VARCHAR(255) DEFAULT NULL,
            nom_region VARCHAR(255) DEFAULT NULL
        )');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE villes');
    }
}