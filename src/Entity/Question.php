<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    const SECRET = 'jinglemybells';

    const TYPE_SIMPLE = 'SIMPLE';
    const TYPE_MULTIPLE = 'MULTIPLE';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description = '';

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bonus;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $answer;

    /**
     * @ORM\Column(type="text")
     */
    private $answers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", mappedBy="questionsAnswered")
     */
    private $teams;

    /**
     * @ORM\Column(type="boolean")
     */
    private $grinchActive = false;

    public function __construct(string $name, int $points, bool $bonus, string $answer)
    {
        $this->name = $name;
        $this->points = $points;
        $this->bonus = $bonus;
        $this->answer = $answer;
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getBonus(): ?bool
    {
        return $this->bonus;
    }

    public function setBonus(bool $bonus): self
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getAnswers(): ?array
    {
        $answers = json_decode($this->answers);
        uksort($answers, function() { return rand() > rand(); });
        return $answers;
    }

    public function setAnswers(array $answers): self
    {
        $this->answers = json_encode($answers);

        return $this;
    }

    public function checkHash(string $hash) : bool
    {
        return ($this->getHash() === $hash);
    }

    public function getHash() : string
    {
        return md5($this->getId() . self::SECRET);
    }

    public function isMultipleChoice() : bool
    {
        return $this->getType() === self::TYPE_MULTIPLE;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->addQuestionsAnswered($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            $team->removeQuestionsAnswered($this);
        }

        return $this;
    }

    public function getGrinchActive(): ?bool
    {
        return $this->grinchActive;
    }

    public function setGrinchActive(bool $grinchActive): self
    {
        $this->grinchActive = $grinchActive;

        return $this;
    }
}
