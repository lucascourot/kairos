<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\CreateExerciseController;

/**
 * @ApiResource(
 *     denormalizationContext={"allow_extra_attributes"=false},
 *     collectionOperations={
 *         "get",
 *         "post_exercise"={
 *             "method"="POST",
 *             "path"="/exercises.{_format}",
 *             "controller"=CreateExerciseController::class,
 *         }
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseRepository")
 */
class Exercise
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="json_array", nullable=true, options={"jsonb": true})
     */
    private $questions;

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

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions): self
    {
        $this->questions = $questions;

        return $this;
    }

    public function areAllMCQValid(): bool
    {
        foreach ($this->questions as $question) {
            $isValidQuestion = false;
            foreach ($question['choices'] as $choice) {
                if ($choice['isCorrect'] === true) {
                    $isValidQuestion = true;
                }
            }

            if ($isValidQuestion === false) {
                return false;
            }
        }

        return true;
    }
}
