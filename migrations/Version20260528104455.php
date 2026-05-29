<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260528104455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE courriers (id SERIAL NOT NULL, createur_id INT DEFAULT NULL, cloture_par_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_validation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, reference VARCHAR(100) NOT NULL, object VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, date_message TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, numero INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2DB3718173A201E5 ON courriers (createur_id)');
        $this->addSql('CREATE INDEX IDX_2DB37181D596D79F ON courriers (cloture_par_id)');
        $this->addSql('COMMENT ON COLUMN courriers.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN courriers.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN courriers.date_validation IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN courriers.date_message IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE fichiers (id SERIAL NOT NULL, message_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, binaire BYTEA DEFAULT NULL, date_fin TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_969DB4AB537A1329 ON fichiers (message_id)');
        $this->addSql('COMMENT ON COLUMN fichiers.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fichiers.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE historiques (id SERIAL NOT NULL, utilisateur_id INT NOT NULL, courrier_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_send BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B25FDE8DFB88E14F ON historiques (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_B25FDE8D8BF41DC7 ON historiques (courrier_id)');
        $this->addSql('COMMENT ON COLUMN historiques.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN historiques.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messages (id SERIAL NOT NULL, courrier_id INT NOT NULL, expediteur_id INT NOT NULL, destinataire_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_validation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_read_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, observation TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB021E968BF41DC7 ON messages (courrier_id)');
        $this->addSql('CREATE INDEX IDX_DB021E9610335F61 ON messages (expediteur_id)');
        $this->addSql('CREATE INDEX IDX_DB021E96A4F84F6E ON messages (destinataire_id)');
        $this->addSql('COMMENT ON COLUMN messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messages.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messages.date_validation IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE roles (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN roles.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN roles.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE utilisateurs (id SERIAL NOT NULL, role_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_497B315ED60322AC ON utilisateurs (role_id)');
        $this->addSql('COMMENT ON COLUMN utilisateurs.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN utilisateurs.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE courriers ADD CONSTRAINT FK_2DB3718173A201E5 FOREIGN KEY (createur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE courriers ADD CONSTRAINT FK_2DB37181D596D79F FOREIGN KEY (cloture_par_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fichiers ADD CONSTRAINT FK_969DB4AB537A1329 FOREIGN KEY (message_id) REFERENCES messages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE historiques ADD CONSTRAINT FK_B25FDE8DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE historiques ADD CONSTRAINT FK_B25FDE8D8BF41DC7 FOREIGN KEY (courrier_id) REFERENCES courriers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E968BF41DC7 FOREIGN KEY (courrier_id) REFERENCES courriers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9610335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateurs ADD CONSTRAINT FK_497B315ED60322AC FOREIGN KEY (role_id) REFERENCES roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE courriers DROP CONSTRAINT FK_2DB3718173A201E5');
        $this->addSql('ALTER TABLE courriers DROP CONSTRAINT FK_2DB37181D596D79F');
        $this->addSql('ALTER TABLE fichiers DROP CONSTRAINT FK_969DB4AB537A1329');
        $this->addSql('ALTER TABLE historiques DROP CONSTRAINT FK_B25FDE8DFB88E14F');
        $this->addSql('ALTER TABLE historiques DROP CONSTRAINT FK_B25FDE8D8BF41DC7');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E968BF41DC7');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E9610335F61');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E96A4F84F6E');
        $this->addSql('ALTER TABLE utilisateurs DROP CONSTRAINT FK_497B315ED60322AC');
        $this->addSql('DROP TABLE courriers');
        $this->addSql('DROP TABLE fichiers');
        $this->addSql('DROP TABLE historiques');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE utilisateurs');
    }
}
