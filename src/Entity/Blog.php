<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Blog
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Заголовок обязательный к заполнению')]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
    private Category|null $category = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User|null $user = null;

    #[ORM\JoinTable(name: 'tags_to_blog')]
    #[ORM\JoinColumn(name: 'blog_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id', unique: true)]
    #[ORM\ManyToMany(targetEntity: 'App\Entity\Tag', cascade: ['persist'])]
    private ArrayCollection|PersistentCollection $tags;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $percent = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, nullable: true, options: ['default' => 'pending'])]
    private ?string $status = 'pending';

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
    private ?DateTime $blockedAt = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'blog', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $comments;

    public function __construct(User|UserInterface $user)
    {
        $this->user = $user;
        $this->comments = new ArrayCollection();
    }

    #[NoReturn]
    #[ORM\PreUpdate]
    public function setBlockedAtValue(): void
    {
        if($this->status == 'blocked' && !$this->blockedAt){
            $this->blockedAt = new DateTime();
        }
    }

    public function addTag(Tag $tag): void
    {
        $this->tags[] = $tag;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTags(): ArrayCollection|PersistentCollection
    {
        return $this->tags;
    }

    public function setTags(ArrayCollection $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPercent(): ?string
    {
        return $this->percent;
    }

    public function setPercent(?string $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getBlockedAt(): ?DateTime
    {
        return $this->blockedAt;
    }

    public function setBlockedAt(?DateTime $blockedAt): static
    {
        $this->blockedAt = $blockedAt;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBlog($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBlog() === $this) {
                $comment->setBlog(null);
            }
        }

        return $this;
    }


}
