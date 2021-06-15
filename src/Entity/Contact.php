<?php
/**
 * Contact entity.
 */

namespace App\Entity;

use App\Repository\ContactRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 * @ORM\Table(name="contacts")
 */
class Contact
{
    /**
     * Primary key.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    private int $id;

    /**
     * First name.
     *
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="2",
     *     max="50"
     * )
     * @Assert\Regex("/^[a-zA-Z\p{L}]+$/u")
     */
    private string $firstName;

    /**
     * Last name.
     *
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="2",
     *     max="50"
     * )
     * @Assert\Regex("/^[a-zA-Z\p{L}]+$/u")
     */
    private string $lastName;

    /**
     * Address.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *     min="3"
     * )
     */
    private string $address;

    /**
     * Phone.
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *     min="3"
     * )
     */
    private string $phone;

    /**
     * Users.
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="contacts")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * Created at datetime.
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private DateTimeInterface $createdAt;

    /**
     * Updated at datetime.
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     */
    private DateTimeInterface $updatedAt;

    /**
     * Events.
     *
     * @ORM\ManyToMany(targetEntity=Event::class, mappedBy="contact")
     */
    private Collection $events;

    /**
     * Contact constructor.
     */
    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    /**
     * Getter for id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for first name.
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Setter for first name.
     *
     * @param string $firstName First name
     *
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Getter for last name.
     *
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Setter for last name.
     *
     * @param string $lastName Last name
     *
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Getter for address.
     *
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Setter for Address.
     *
     * @param string $address Address
     *
     * @return $this
     */
    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Getter for Phone.
     *
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Setter for Phone.
     *
     * @param string $phone Phone
     *
     * @return Contact
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Getter for user.
     *
     * @return User|null User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param User $user User
     *
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Getter for created at.
     *
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param DateTimeInterface $createdAt Created at
     *
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Getter for updated at.
     *
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     *
     * @param DateTimeInterface $updatedAt Updated at
     *
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Getter events.
     *
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * Add event.
     *
     * @param Event $event Event entity
     *
     * @return $this
     */
    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addContact($this);
        }

        return $this;
    }

    /**
     * Remove event.
     *
     * @param Event $event Event entity
     *
     * @return $this
     */
    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            $event->removeContact($this);
        }

        return $this;
    }
}
