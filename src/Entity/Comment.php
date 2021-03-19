<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\Table(name="comments")
 */
class Comment
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var text
     * @ORM\Column(type="text")
     */
    protected $message;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @var datetime
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments",cascade={"persist"}))
     */
    protected $author;

    /**
     * @var Post
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="posts")
     */
    protected $post;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param text $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return datetime
     */
    public function getCreatedAt(): ?\DateTime
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
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return Post
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    }


}