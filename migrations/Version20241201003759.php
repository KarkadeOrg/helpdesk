<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241201003759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ticket_status (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(63) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE ticket ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA36BF700BD FOREIGN KEY (status_id) REFERENCES ticket_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_97A0ADA36BF700BD ON ticket (status_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA36BF700BD');
        $this->addSql('DROP TABLE ticket_status');
        $this->addSql('DROP INDEX IDX_97A0ADA36BF700BD');
        $this->addSql('ALTER TABLE ticket DROP status_id');
    }
}
