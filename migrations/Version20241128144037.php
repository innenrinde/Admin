<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128144037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE indicator ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE indicator ADD CONSTRAINT FK_D1349DB312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_D1349DB312469DE2 ON indicator (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE indicator DROP FOREIGN KEY FK_D1349DB312469DE2');
        $this->addSql('DROP INDEX IDX_D1349DB312469DE2 ON indicator');
        $this->addSql('ALTER TABLE indicator DROP category_id');
    }
}
