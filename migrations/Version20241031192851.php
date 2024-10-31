<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031192851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog ADD created_at DATETIME default NULL, ADD updated_at DATETIME default NULL');
        $this->addSql('ALTER TABLE category ADD created_at DATETIME default NULL, ADD updated_at DATETIME default NULL');
        $this->addSql('ALTER TABLE tag ADD created_at DATETIME default NULL, ADD updated_at DATETIME default NULL');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME default NULL, ADD updated_at DATETIME default NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE category DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE tag DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE user DROP created_at, DROP updated_at');
    }
}
