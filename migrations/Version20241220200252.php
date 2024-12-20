<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220200252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add author to messages';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE message ADD author_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN message.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B6BD307FF675F31B ON message (author_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FF675F31B');
        $this->addSql('DROP INDEX IDX_B6BD307FF675F31B');
        $this->addSql('ALTER TABLE message DROP author_id');
    }
}
