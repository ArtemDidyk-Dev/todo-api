<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240902172909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add expiring';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task ADD expiring TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task DROP expiring');
    }
}
