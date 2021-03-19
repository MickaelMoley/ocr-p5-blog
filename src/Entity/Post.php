<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\Table(name="posts")
 */
class Post
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $excerpt;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;
    /**
     * @var datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;
    /**
     * @var datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     */
    protected $author;

    /**
     * @var Comment
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     */
    protected $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    /**
     * @param string $excerpt
     */
    public function setExcerpt(string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return datetime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param datetime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param datetime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return Comment
     */
    public function getComments()
    {
        return $this->comments;
    }








}