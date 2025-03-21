<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321194718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB258C03F15C8C03F15C');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258C03F15C8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB258C03F15C8C03F15C');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258C03F15C8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
