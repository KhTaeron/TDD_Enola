<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228142731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribers ADD id VARCHAR(255) NOT NULL, CHANGE code code VARCHAR(20) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FCD16AC77153098 ON subscribers (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribers MODIFY id VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_2FCD16AC77153098 ON subscribers');
        $this->addSql('DROP INDEX `PRIMARY` ON subscribers');
        $this->addSql('ALTER TABLE subscribers DROP id, CHANGE code code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscribers ADD PRIMARY KEY (code)');
    }
}
