<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
{
    const SECRET = 'santaisnotthesameasthesint';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $points = 0;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Question", inversedBy="teams")
     */
    private $questionsAnswered;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WrongAnswer", mappedBy="team", orphanRemoval=true,cascade={"persist"})
     */
    private $wrongAnswers;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activeBonus;

    /**
     * @ORM\Column(type="boolean")
     */
    private $passiveBonus;

    /**
     * @ORM\Column(type="integer")
     */
    private $stolenGifts = 0;

    public function __construct()
    {
        $this->questionsAnswered = new ArrayCollection();
        $this->wrongAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPoints(): int
    {
        return $this->points;
    }

    private function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function addPoints(Question $question)
    {
        $points = $question->getPoints();
        if($this->getActiveBonus()) {
            $points *= 2;
            $this->setActiveBonus(false);
        }

        $this->setPoints($this->getPoints() + $points);

        $this->addQuestionsAnswered($question);
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestionsAnswered(): Collection
    {
        return $this->questionsAnswered;
    }

    public function addQuestionsAnswered(Question $questionsAnswered): self
    {
        if (!$this->questionsAnswered->contains($questionsAnswered)) {
            $this->questionsAnswered[] = $questionsAnswered;
        }

        return $this;
    }

    public function removeQuestionsAnswered(Question $questionsAnswered): self
    {
        if ($this->questionsAnswered->contains($questionsAnswered)) {
            $this->questionsAnswered->removeElement($questionsAnswered);
        }

        return $this;
    }

    public function hasQuestionAnswered(Question $questionsAnswered) : bool
    {
        return $this->questionsAnswered->contains($questionsAnswered);
    }

    /**
     * @return Collection|WrongAnswer[]
     */
    public function getWrongAnswers(): Collection
    {
        return $this->wrongAnswers;
    }

    public function addWrongAnswer(WrongAnswer $wrongAnswer): self
    {
        /** @var WrongAnswer $wrongAnswerStored */
        foreach ($this->wrongAnswers AS $wrongAnswerStored) {
            if($wrongAnswer->getQuestion()->getId() === $wrongAnswerStored->getId()) {
                $wrongAnswerStored->updateTimestamp();
                return $this;
            }
        }

        if (!$this->wrongAnswers->contains($wrongAnswer)) {
            if($wrongAnswer->getTeam()->getId() != $this->getId()) {
                throw new \DomainException('Invalid wrong answer: does not belong to team');
            }

            $this->wrongAnswers[] = $wrongAnswer;
        }

        return $this;
    }

    public function removeWrongAnswer(WrongAnswer $wrongAnswer): self
    {
        if ($this->wrongAnswers->contains($wrongAnswer)) {
            $this->wrongAnswers->removeElement($wrongAnswer);
            // set the owning side to null (unless already changed)
            if ($wrongAnswer->getTeam() === $this) {
                $wrongAnswer->setTeam(null);
            }
        }

        return $this;
    }

    public function canAnswerInSeconds(Question $question) : int
    {
        /** @var WrongAnswer $wrongAnswer */
        foreach($this->wrongAnswers AS $wrongAnswer) {
            if($wrongAnswer->getQuestion()->getId() === $question->getId()) {
                $diff = $wrongAnswer->getTimestamp()->getTimestamp() - (new \DateTimeImmutable())->getTimestamp();

                return max(0, $diff);
            }
        }

        return 0;//can answer now
    }

    public function getActiveBonus(): ?bool
    {
        return $this->activeBonus;
    }

    public function setActiveBonus(bool $activeBonus): self
    {
        $this->activeBonus = $activeBonus;

        return $this;
    }

    public function getPassiveBonus(): ?bool
    {
        return $this->passiveBonus;
    }

    public function setPassiveBonus(bool $passiveBonus): self
    {
        $this->passiveBonus = $passiveBonus;

        return $this;
    }

    public function checkHash(string $hash) : bool
    {
        return $hash == $this->getHash();
    }

    public function getHash() : string
    {
        return md5($this->id . self::SECRET);
    }

    public function getStolenGifts(): ?int
    {
        return $this->stolenGifts;
    }

    public function setStolenGifts(int $stolenGifts): self
    {
        $this->stolenGifts = $stolenGifts;

        return $this;
    }

    public function stealGift() : int
    {
        $amount = rand(1, min(5, $this->points));

        $this->points -= $amount;
        $this->stolenGifts += $amount;

        return $amount;
    }

    public function returnGift(int $amount)
    {
        if($amount > $this->stolenGifts) {
            $amount = $this->stolenGifts;
        }

        $this->points += $amount;
        $this->stolenGifts -= $amount;
    }
}
