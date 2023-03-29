<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230329062617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE big_sur (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(50) NOT NULL, pid VARCHAR(50) NOT NULL, access INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE big_sur_draft (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(50) NOT NULL, pid VARCHAR(50) NOT NULL, title VARCHAR(200) DEFAULT NULL, body LONGTEXT DEFAULT NULL, access INT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE big_sur_fkscore (id INT AUTO_INCREMENT NOT NULL, pid VARCHAR(50) NOT NULL, grade DOUBLE PRECISION NOT NULL, ease DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE big_sur_list (id INT AUTO_INCREMENT NOT NULL, pid VARCHAR(50) NOT NULL, state VARCHAR(10) NOT NULL, title LONGTEXT NOT NULL, access INT NOT NULL, cover VARCHAR(100) DEFAULT NULL, cover_extension VARCHAR(50) DEFAULT NULL, date DATETIME NOT NULL, note LONGTEXT NOT NULL, parags INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE big_sur_mute (id INT AUTO_INCREMENT NOT NULL, follower VARCHAR(50) NOT NULL, following VARCHAR(50) NOT NULL, state INT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE big_sur_subscribes (id INT AUTO_INCREMENT NOT NULL, follower VARCHAR(50) NOT NULL, following VARCHAR(50) NOT NULL, state INT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_articles (id INT AUTO_INCREMENT NOT NULL, hid VARCHAR(50) NOT NULL, title VARCHAR(200) NOT NULL, body LONGTEXT NOT NULL, sub_section_id VARCHAR(50) DEFAULT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_response (id INT AUTO_INCREMENT NOT NULL, hid VARCHAR(50) NOT NULL, date DATETIME NOT NULL, response LONGTEXT DEFAULT NULL, suggestion LONGTEXT DEFAULT NULL, uid VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_sections (id INT AUTO_INCREMENT NOT NULL, section_id VARCHAR(10) NOT NULL, section_name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_confirm (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(50) DEFAULT NULL, username VARCHAR(50) DEFAULT NULL, state INT DEFAULT NULL, code VARCHAR(10) DEFAULT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_diamond (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(50) NOT NULL, password VARCHAR(255) NOT NULL, confirmed INT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_onyx (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(50) NOT NULL, seshkey VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_recover (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(50) NOT NULL, state INT NOT NULL, code VARCHAR(10) NOT NULL, change_key VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_sapphire (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(50) NOT NULL, about LONGTEXT DEFAULT NULL, cover VARCHAR(100) NOT NULL, date DATETIME NOT NULL, display VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, followers INT DEFAULT NULL, following INT DEFAULT NULL, location VARCHAR(50) DEFAULT NULL, name VARCHAR(50) DEFAULT NULL, state INT DEFAULT NULL, uname VARCHAR(50) NOT NULL, views INT DEFAULT NULL, website VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_secure (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(50) NOT NULL, passcount INT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_visitor (id INT AUTO_INCREMENT NOT NULL, v_id VARCHAR(50) NOT NULL, v_name VARCHAR(50) DEFAULT NULL, v_about VARCHAR(50) DEFAULT NULL, visits INT DEFAULT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verb_comments (id INT AUTO_INCREMENT NOT NULL, pid VARCHAR(50) NOT NULL, puid VARCHAR(50) NOT NULL, uid VARCHAR(50) NOT NULL, cid VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verb_comments_list (id INT AUTO_INCREMENT NOT NULL, cid VARCHAR(50) NOT NULL, comment LONGTEXT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verb_likes (id INT AUTO_INCREMENT NOT NULL, pid VARCHAR(50) NOT NULL, puid VARCHAR(50) NOT NULL, uid VARCHAR(50) NOT NULL, lid VARCHAR(50) NOT NULL, state INT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verb_report (id INT AUTO_INCREMENT NOT NULL, report_id VARCHAR(50) NOT NULL, pid VARCHAR(50) NOT NULL, uid VARCHAR(50) NOT NULL, sitch VARCHAR(50) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verb_saves (id INT AUTO_INCREMENT NOT NULL, pid VARCHAR(50) NOT NULL, puid VARCHAR(50) NOT NULL, uid VARCHAR(50) NOT NULL, bid VARCHAR(50) NOT NULL, state INT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verb_shares (id INT AUTO_INCREMENT NOT NULL, share_id VARCHAR(50) NOT NULL, pid VARCHAR(50) NOT NULL, uid VARCHAR(50) NOT NULL, media VARCHAR(10) DEFAULT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verb_unlikes (id INT AUTO_INCREMENT NOT NULL, pid VARCHAR(50) NOT NULL, puid VARCHAR(50) NOT NULL, uid VARCHAR(50) NOT NULL, lid VARCHAR(50) NOT NULL, state INT DEFAULT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verb_visits (id INT AUTO_INCREMENT NOT NULL, visit_id VARCHAR(50) NOT NULL, pid VARCHAR(50) NOT NULL, uid VARCHAR(50) NOT NULL, media VARCHAR(10) DEFAULT NULL, state INT DEFAULT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE big_sur');
        $this->addSql('DROP TABLE big_sur_draft');
        $this->addSql('DROP TABLE big_sur_fkscore');
        $this->addSql('DROP TABLE big_sur_list');
        $this->addSql('DROP TABLE big_sur_mute');
        $this->addSql('DROP TABLE big_sur_subscribes');
        $this->addSql('DROP TABLE help_articles');
        $this->addSql('DROP TABLE help_response');
        $this->addSql('DROP TABLE help_sections');
        $this->addSql('DROP TABLE user_confirm');
        $this->addSql('DROP TABLE user_diamond');
        $this->addSql('DROP TABLE user_onyx');
        $this->addSql('DROP TABLE user_recover');
        $this->addSql('DROP TABLE user_sapphire');
        $this->addSql('DROP TABLE user_secure');
        $this->addSql('DROP TABLE user_visitor');
        $this->addSql('DROP TABLE verb_comments');
        $this->addSql('DROP TABLE verb_comments_list');
        $this->addSql('DROP TABLE verb_likes');
        $this->addSql('DROP TABLE verb_report');
        $this->addSql('DROP TABLE verb_saves');
        $this->addSql('DROP TABLE verb_shares');
        $this->addSql('DROP TABLE verb_unlikes');
        $this->addSql('DROP TABLE verb_visits');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
