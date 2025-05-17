<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514134712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE object_data ADD object_begin_date INT DEFAULT NULL, ADD object_end_date INT DEFAULT NULL, ADD dimensions LONGTEXT DEFAULT NULL, ADD country LONGTEXT DEFAULT NULL, ADD object_url LONGTEXT DEFAULT NULL, CHANGE info2 culture LONGTEXT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE object_data ADD info2 LONGTEXT DEFAULT NULL, DROP culture, DROP object_begin_date, DROP object_end_date, DROP dimensions, DROP country, DROP object_url
        SQL);
    }
}
