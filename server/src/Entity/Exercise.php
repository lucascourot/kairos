<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\Constraints\MCQ;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

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
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "type"="string",
     *             "example"="About Front-End development..."
     *         }
     *     }
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="json_array", nullable=true, options={"jsonb": true})
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "type"="object",
     *             "example"={
     *                 "type": "MCQ",
     *                 "label": "What's the most frequently used JS framework in our company?",
     *                 "choices": {
     *                      {
     *                          "isCorrect": false,
     *                          "label": "Vue.js"
     *                      },
     *                      {
     *                          "isCorrect": false,
     *                          "label": "Ember"
     *                      },
     *                      {
     *                          "isCorrect": true,
     *                          "label": "React"
     *                      },
     *                      {
     *                          "isCorrect": false,
     *                          "label": "Angular"
     *                      },
     *                 }
     *             }
     *         }
     *     }
     * )
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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());

        $metadata->addPropertyConstraint(
            'questions',
            new Assert\All([
                new Assert\Collection([
                    'fields' => [
                        'type' => new Assert\Required([new Assert\Choice(['MCQ'])]),
                        'label' => new Assert\Required([new Assert\NotBlank()]),
                        'choices' => new Assert\Optional([
                            new Assert\All([
                                new Assert\Collection([
                                    'fields' => [
                                        'isCorrect' => new Assert\Required([new Assert\Type(['type' => 'boolean'])]),
                                        'label' => new Assert\Required([new Assert\NotBlank()])
                                    ]
                                ])
                            ])
                        ])
                    ]
                ]),
                new MCQ(),
            ])
        );
    }
}
