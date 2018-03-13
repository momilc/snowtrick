<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 * @ORM\Table(name="snowtrick_figure")

 */
class Figure
{
    /**
     */
    public const NUM_ITEMS = 10;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     * @ORM\JoinTable(name="snowtrick_figure_title")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ORM\JoinTable(name="snowtrick_figure_slug")
     */
    private $slug;


    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     * @ORM\JoinTable(name="snowtrick_figure_content")
     * @Assert\NotBlank(message="figure.blank_content")
     * @Assert\Length(min=10, minMessage="figure.too_short_content")
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $publishedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\JoinTable(name="snowtrick_figure_author")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", cascade={"persist"})
     * @ORM\JoinTable(name="snowtrick_figure_category")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Style", cascade={"persist"})
     * @ORM\JoinTable(name="snowtrick_figure_style")
     * @ORM\JoinColumn(nullable=false)
     */
    private $style;

    /**
     * @var Video[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Video", cascade={"persist"})
     * @ORM\JoinTable(name="snowtrick_figure_videos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $videos;

    /**
     * @var Comment[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Comment",
     *      mappedBy="figure",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\JoinTable(name="snowtrick_figure_comments")
     * @ORM\OrderBy({"publishedAt": "DESC"})
     */
    private $comments;


    public function __construct()
    {
        $this->publishedAt = new DateTime();
        $this->comments = new ArrayCollection();
        $this->videos = new ArrayCollection();

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }


    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(?Comment $comment): void
    {
        $comment->setFigure($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }

    public function removeComment(Comment $comment): void
    {
        $comment->setFigure(null);
        $this->comments->removeElement($comment);
    }



    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }


    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage(Image $image): void
    {
        $this->image = $image;
    }

    /**
     * @param mixed $style
     */
    public function setStyle(Style $style): void
    {
        $this->style = $style;
    }


    // Notez le pluriel, on récupère une liste de catégories ici !
    public function getStyle()
    {
      return $this->style;
    }

    /**
     * @return Video[]|ArrayCollection
     */
    public function getVideos() : Collection
    {
        return $this->videos;
    }

    /**
     * @param Video[]|ArrayCollection $videos
     */
    public function setVideos($videos): void
    {
        $this->videos = $videos;
    }

    public function addVideo(?Video ...$videos)
    {
        foreach ($videos as $video) {
            if (!$this->videos->contains($video)) {
                $this->videos->add($video);
            }
        }
    }
    public function removeVideo(Video $video)
    {
        $this->videos->removeElement($video);
    }



}
