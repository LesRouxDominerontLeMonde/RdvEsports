<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212154852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rdv_cat (rdv_id INT NOT NULL, cat_id INT NOT NULL, INDEX IDX_F11DC3114CCE3F86 (rdv_id), INDEX IDX_F11DC311E6ADA943 (cat_id), PRIMARY KEY(rdv_id, cat_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rdv_cat ADD CONSTRAINT FK_F11DC3114CCE3F86 FOREIGN KEY (rdv_id) REFERENCES rdv (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rdv_cat ADD CONSTRAINT FK_F11DC311E6ADA943 FOREIGN KEY (cat_id) REFERENCES cat (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv_cat DROP FOREIGN KEY FK_F11DC3114CCE3F86');
        $this->addSql('ALTER TABLE rdv_cat DROP FOREIGN KEY FK_F11DC311E6ADA943');
        $this->addSql('DROP TABLE rdv_cat');
    }
}
