<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404120909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE assessment (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, target_id INT NOT NULL, topic_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_F7523D70953C1C61 (source_id), INDEX IDX_F7523D70158E0B66 (target_id), INDEX IDX_F7523D701F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE developer (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, name VARCHAR(255) NOT NULL, `key` VARCHAR(255) NOT NULL, INDEX IDX_65FB8B9A296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_9D40DE1B296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assessment ADD CONSTRAINT FK_F7523D70953C1C61 FOREIGN KEY (source_id) REFERENCES developer (id)');
        $this->addSql('ALTER TABLE assessment ADD CONSTRAINT FK_F7523D70158E0B66 FOREIGN KEY (target_id) REFERENCES developer (id)');
        $this->addSql('ALTER TABLE assessment ADD CONSTRAINT FK_F7523D701F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE developer ADD CONSTRAINT FK_65FB8B9A296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assessment DROP FOREIGN KEY FK_F7523D70953C1C61');
        $this->addSql('ALTER TABLE assessment DROP FOREIGN KEY FK_F7523D70158E0B66');
        $this->addSql('ALTER TABLE developer DROP FOREIGN KEY FK_65FB8B9A296CD8AE');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B296CD8AE');
        $this->addSql('ALTER TABLE assessment DROP FOREIGN KEY FK_F7523D701F55203D');
        $this->addSql('DROP TABLE assessment');
        $this->addSql('DROP TABLE developer');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE topic');
    }
}
