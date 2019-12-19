<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WrongAnswerRepository")
 */
class WrongAnswer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="wrongAnswers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    public function __construct(Team $team, Question $question)
    {
        $this->team = $team;
        $this->question = $question;
        $this->updateTimestamp();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }


    public function getTimestamp(): \DateTimeInterface
    {
        return $this->timestamp;
    }

    public function updateTimestamp() : void
    {
        $this->timestamp = new \DateTimeImmutable("+2 minute");
    }
}