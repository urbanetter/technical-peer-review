<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220707204756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds confidence table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE confidence (id INT AUTO_INCREMENT NOT NULL, topic_id INT NOT NULL, developer_id INT NOT NULL, confidence INT NOT NULL, INDEX IDX_97791911F55203D (topic_id), INDEX IDX_977919164DD9267 (developer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE confidence ADD CONSTRAINT FK_97791911F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE confidence ADD CONSTRAINT FK_977919164DD9267 FOREIGN KEY (developer_id) REFERENCES developer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE confidence');
    }
}
