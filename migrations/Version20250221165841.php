<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221165841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv ADD author_id INT NOT NULL, DROP author');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F86F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_10C31F86F675F31B ON rdv (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F86F675F31B');
        $this->addSql('DROP INDEX IDX_10C31F86F675F31B ON rdv');
        $this->addSql('ALTER TABLE rdv ADD author VARCHAR(255) NOT NULL, DROP author_id');
    }
}
