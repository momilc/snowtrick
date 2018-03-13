<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180311221444 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE snowtrick_figure (id INT AUTO_INCREMENT NOT NULL, image_id INT NOT NULL, author_id INT NOT NULL, category_id INT NOT NULL, style_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_A701A4232B36786B (title), UNIQUE INDEX UNIQ_A701A4233DA5256D (image_id), INDEX IDX_A701A423F675F31B (author_id), INDEX IDX_A701A42312469DE2 (category_id), INDEX IDX_A701A423BACD6074 (style_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snowtrick_figure_videos (figure_id INT NOT NULL, video_id INT NOT NULL, INDEX IDX_419DFB445C011B5 (figure_id), INDEX IDX_419DFB4429C1004E (video_id), PRIMARY KEY(figure_id, video_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snowtrick_user (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, profile_picture_path VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1ED50031F85E0677 (username), UNIQUE INDEX UNIQ_1ED50031E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snowtrick_category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snowtrick_style (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snowtrick_video (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snowtrick_comment (id INT AUTO_INCREMENT NOT NULL, figure_id INT NOT NULL, author_id INT NOT NULL, photo_id INT DEFAULT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_864BED2B5C011B5 (figure_id), INDEX IDX_864BED2BF675F31B (author_id), INDEX IDX_864BED2B7E9E4C8C (photo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snowtrick_photo (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snowtrick_image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE snowtrick_figure ADD CONSTRAINT FK_A701A4233DA5256D FOREIGN KEY (image_id) REFERENCES snowtrick_image (id)');
        $this->addSql('ALTER TABLE snowtrick_figure ADD CONSTRAINT FK_A701A423F675F31B FOREIGN KEY (author_id) REFERENCES snowtrick_user (id)');
        $this->addSql('ALTER TABLE snowtrick_figure ADD CONSTRAINT FK_A701A42312469DE2 FOREIGN KEY (category_id) REFERENCES snowtrick_category (id)');
        $this->addSql('ALTER TABLE snowtrick_figure ADD CONSTRAINT FK_A701A423BACD6074 FOREIGN KEY (style_id) REFERENCES snowtrick_style (id)');
        $this->addSql('ALTER TABLE snowtrick_figure_videos ADD CONSTRAINT FK_419DFB445C011B5 FOREIGN KEY (figure_id) REFERENCES snowtrick_figure (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE snowtrick_figure_videos ADD CONSTRAINT FK_419DFB4429C1004E FOREIGN KEY (video_id) REFERENCES snowtrick_video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE snowtrick_comment ADD CONSTRAINT FK_864BED2B5C011B5 FOREIGN KEY (figure_id) REFERENCES snowtrick_figure (id)');
        $this->addSql('ALTER TABLE snowtrick_comment ADD CONSTRAINT FK_864BED2BF675F31B FOREIGN KEY (author_id) REFERENCES snowtrick_user (id)');
        $this->addSql('ALTER TABLE snowtrick_comment ADD CONSTRAINT FK_864BED2B7E9E4C8C FOREIGN KEY (photo_id) REFERENCES snowtrick_photo (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE snowtrick_figure_videos DROP FOREIGN KEY FK_419DFB445C011B5');
        $this->addSql('ALTER TABLE snowtrick_comment DROP FOREIGN KEY FK_864BED2B5C011B5');
        $this->addSql('ALTER TABLE snowtrick_figure DROP FOREIGN KEY FK_A701A423F675F31B');
        $this->addSql('ALTER TABLE snowtrick_comment DROP FOREIGN KEY FK_864BED2BF675F31B');
        $this->addSql('ALTER TABLE snowtrick_figure DROP FOREIGN KEY FK_A701A42312469DE2');
        $this->addSql('ALTER TABLE snowtrick_figure DROP FOREIGN KEY FK_A701A423BACD6074');
        $this->addSql('ALTER TABLE snowtrick_figure_videos DROP FOREIGN KEY FK_419DFB4429C1004E');
        $this->addSql('ALTER TABLE snowtrick_comment DROP FOREIGN KEY FK_864BED2B7E9E4C8C');
        $this->addSql('ALTER TABLE snowtrick_figure DROP FOREIGN KEY FK_A701A4233DA5256D');
        $this->addSql('DROP TABLE snowtrick_figure');
        $this->addSql('DROP TABLE snowtrick_figure_videos');
        $this->addSql('DROP TABLE snowtrick_user');
        $this->addSql('DROP TABLE snowtrick_category');
        $this->addSql('DROP TABLE snowtrick_style');
        $this->addSql('DROP TABLE snowtrick_video');
        $this->addSql('DROP TABLE snowtrick_comment');
        $this->addSql('DROP TABLE snowtrick_photo');
        $this->addSql('DROP TABLE snowtrick_image');
    }
}
