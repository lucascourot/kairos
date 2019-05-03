<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\Constraints\MCQ;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     denormalizationContext={"allow_extra_attributes"=false}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseRepository")
 */
class Exercise
{
    /** @var string */
    public const TYPE_MCQ = 'MCQ';

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
     * @Assert\All({
     *     @Assert\Collection(
     *         fields={
     *             "type" = @Assert\Required({@Assert\Choice({"MCQ"})}),
     *             "label" = @Assert\Required({@Assert\NotBlank()}),
     *             "choices" = @Assert\Optional({
     *                 @Assert\All({
     *                     @Assert\Collection(
     *                         fields={
     *                             "isCorrect" = @Assert\Required({@Assert\Type(type="boolean")}),
     *                             "label" = @Assert\Required({@Assert\NotBlank()})
     *                         }
     *                    )
     *                 })
     *             })
     *         }
     *     ),
     *     @MCQ,
     * })
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
}
