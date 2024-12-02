<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241202223731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add default ticket statuses';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO public.ticket_status (name) VALUES ('Open'), ('Pending'), ('Closed');");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM public.ticket_status WHERE name IN ('Open', 'Pending', 'Closed');");
    }
}
