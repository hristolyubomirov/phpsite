<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250303094621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Tables biph, word, sentence and image initial data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(file_get_contents(__DIR__ . '/biph.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/word.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/image.sql'));
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE IF EXISTS survey;");
        $this->addSql("DROP TABLE IF EXISTS screen;");
        $this->addSql("DROP TABLE IF EXISTS image;");
        $this->addSql("DROP TABLE IF EXISTS word;");
        $this->addSql("DROP TABLE IF EXISTS biph;");
    }
}
