<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for Brasil Burger manager (users, burgers, menus, complements, zones, orders, order_items)';
    }

    public function up(Schema $schema): void
    {
        // Only create tables if they do not already exist to avoid duplicate table errors
        if (!$schema->hasTable('clients')) {
            $this->addSql('CREATE TABLE clients (
                id SERIAL NOT NULL,
                email VARCHAR(180) DEFAULT NULL,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                telephone VARCHAR(20) NOT NULL,
                password VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_CLIENT_EMAIL ON clients (email)');
            $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_CLIENT_TELEPHONE ON clients (telephone)');
        }

        if (!$schema->hasTable('gestionnaires')) {
            $this->addSql('CREATE TABLE gestionnaires (
                id SERIAL NOT NULL,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                password VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_GESTIONNAIRE_EMAIL ON gestionnaires (email)');
        }

        if (!$schema->hasTable('burgers')) {
            $this->addSql('CREATE TABLE burgers (
                id SERIAL NOT NULL,
                nom VARCHAR(100) NOT NULL,
                prix NUMERIC(10,2) NOT NULL,
                image VARCHAR(255) DEFAULT NULL,
                archive BOOLEAN NOT NULL,
                PRIMARY KEY(id)
            )');
        }

        if (!$schema->hasTable('complements')) {
            $this->addSql('CREATE TABLE complements (
                id SERIAL NOT NULL,
                nom VARCHAR(100) NOT NULL,
                prix NUMERIC(10,2) NOT NULL,
                image VARCHAR(255) DEFAULT NULL,
                type VARCHAR(20) NOT NULL,
                archive BOOLEAN NOT NULL,
                PRIMARY KEY(id)
            )');
        }

        if (!$schema->hasTable('menus')) {
            $this->addSql('CREATE TABLE menus (
                id SERIAL NOT NULL,
                nom VARCHAR(100) NOT NULL,
                burger_id INT NOT NULL,
                boisson_id INT NOT NULL,
                frite_id INT NOT NULL,
                image VARCHAR(255) DEFAULT NULL,
                archive BOOLEAN NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX IF NOT EXISTS IDX_MENU_BURGER ON menus (burger_id)');
            $this->addSql('CREATE INDEX IF NOT EXISTS IDX_MENU_BOISSON ON menus (boisson_id)');
            $this->addSql('CREATE INDEX IF NOT EXISTS IDX_MENU_FRITE ON menus (frite_id)');
            $this->addSql('ALTER TABLE IF EXISTS menus ADD CONSTRAINT IF NOT EXISTS FK_MENU_BURGER FOREIGN KEY (burger_id) REFERENCES burgers (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE IF EXISTS menus ADD CONSTRAINT IF NOT EXISTS FK_MENU_BOISSON FOREIGN KEY (boisson_id) REFERENCES complements (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE IF EXISTS menus ADD CONSTRAINT IF NOT EXISTS FK_MENU_FRITE FOREIGN KEY (frite_id) REFERENCES complements (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        }

        if (!$schema->hasTable('zones')) {
            $this->addSql('CREATE TABLE zones (
                id SERIAL NOT NULL,
                nom VARCHAR(100) NOT NULL,
                quartiers TEXT DEFAULT NULL,
                prix NUMERIC(10,2) NOT NULL,
                PRIMARY KEY(id)
            )');
        }

        if (!$schema->hasTable('livreurs')) {
            $this->addSql('CREATE TABLE livreurs (
                id SERIAL NOT NULL,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                telephone VARCHAR(20) NOT NULL,
                PRIMARY KEY(id)
            )');
        }

        if (!$schema->hasTable('commandes')) {
            $this->addSql('CREATE TABLE commandes (
                id SERIAL NOT NULL,
                client_id INT NOT NULL,
                zone_id INT DEFAULT NULL,
                type VARCHAR(20) NOT NULL,
                etat VARCHAR(20) NOT NULL,
                total NUMERIC(10,2) NOT NULL,
                date_commande TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                adresse VARCHAR(255) DEFAULT NULL,
                livreur_id INT DEFAULT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX IF NOT EXISTS IDX_COMMANDES_CLIENT ON commandes (client_id)');
            $this->addSql('CREATE INDEX IF NOT EXISTS IDX_COMMANDES_ZONE ON commandes (zone_id)');
            $this->addSql('ALTER TABLE IF EXISTS commandes ADD CONSTRAINT IF NOT EXISTS FK_COMMANDES_CLIENT FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE IF EXISTS commandes ADD CONSTRAINT IF NOT EXISTS FK_COMMANDES_ZONE FOREIGN KEY (zone_id) REFERENCES zones (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE IF EXISTS commandes ADD CONSTRAINT IF NOT EXISTS FK_COMMANDES_LIVREUR FOREIGN KEY (livreur_id) REFERENCES livreurs (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        }

        if (!$schema->hasTable('lignes_commande')) {
            $this->addSql('CREATE TABLE lignes_commande (
                id SERIAL NOT NULL,
                commande_id INT NOT NULL,
                burger_id INT DEFAULT NULL,
                menu_id INT DEFAULT NULL,
                complement_id INT DEFAULT NULL,
                quantite INT NOT NULL,
                prix_unitaire NUMERIC(10,2) NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE INDEX IF NOT EXISTS IDX_LIGNES_COMMANDE_COMMAND ON lignes_commande (commande_id)');
            $this->addSql('ALTER TABLE IF EXISTS lignes_commande ADD CONSTRAINT IF NOT EXISTS FK_LIGNES_COMMANDE_COMMAND FOREIGN KEY (commande_id) REFERENCES commandes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        }

        if (!$schema->hasTable('paiements')) {
            $this->addSql('CREATE TABLE paiements (
                id SERIAL NOT NULL,
                commande_id INT NOT NULL,
                date_paiement TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                montant NUMERIC(10,2) NOT NULL,
                methode VARCHAR(10) NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_PAIEMENTS_COMMANDE ON paiements (commande_id)');
            $this->addSql('ALTER TABLE IF EXISTS paiements ADD CONSTRAINT IF NOT EXISTS FK_PAIEMENTS_COMMANDE FOREIGN KEY (commande_id) REFERENCES commandes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE paiements DROP CONSTRAINT FK_PAIEMENTS_COMMANDE');
        $this->addSql('ALTER TABLE lignes_commande DROP CONSTRAINT FK_LIGNES_COMMANDE_COMMAND');
        $this->addSql('ALTER TABLE commandes DROP CONSTRAINT FK_COMMANDES_LIVREUR');
        $this->addSql('ALTER TABLE commandes DROP CONSTRAINT FK_COMMANDES_ZONE');
        $this->addSql('ALTER TABLE commandes DROP CONSTRAINT FK_COMMANDES_CLIENT');

        $this->addSql('DROP TABLE paiements');
        $this->addSql('DROP TABLE lignes_commande');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE livreurs');
        $this->addSql('DROP TABLE zones');
        $this->addSql('DROP TABLE menus');
        $this->addSql('DROP TABLE complements');
        $this->addSql('DROP TABLE burgers');
        $this->addSql('DROP TABLE gestionnaires');
        $this->addSql('DROP TABLE clients');
    }
}


