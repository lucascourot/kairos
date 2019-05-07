<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\Constraints\MCQ;
use App\Validator\Constraints\Question;
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
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "type"="string",
     *             "example"="About the weather..."
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
     *             "type"="array",
     *             "items"={
     *                 "properties"={
     *                     "type"={"type"="string", "example"="MCQ"},
     *                     "label"={"type"="string", "example"="What's the weather like?"},
     *                     "choices"={
     *                         "type"="array",
     *                         "items"={
     *                             "type"="object",
     *                             "properties"={
     *                                 "isCorrect"={"type"="boolean", "example"=false},
     *                                 "label"={"type"="string", "example"="Cloudy"}
     *                             }
     *                         }
     *                     }
     *                 }
     *             },
     *             "example"={
     *                 {
     *                     "type"="MCQ",
     *                     "label"="What's the weather like today?",
     *                     "choices"={
     *                         {"isCorrect"=true, "label"="Sunny"},
     *                         {"isCorrect"=false, "label"="Cloudy"},
     *                         {"isCorrect"=false, "label"="Windy"},
     *                         {"isCorrect"=false, "label"="Rainy"}
     *                     }
     *                 },
     *                 {
     *                     "type"="MCQ",
     *                     "label"="What is the color of a cloud?",
     *                     "choices"={
     *                         {"isCorrect"=false, "label"="green"},
     *                         {"isCorrect"=true, "label"="white"},
     *                         {"isCorrect"=true, "label"="grey"},
     *                         {"isCorrect"=false, "label"="blue"}
     *                     }
     *                 },
     *              }
     *         }
     *     }
     * )
     */
    private $questions;

    public function getId(): ?string
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

        $metadata->addPropertyConstraints(
            'questions',
            [
            new Question(),
            new Assert\All([
                new Assert\Collection([
                    'fields' => [
                        'position' => new Assert\Required(new Assert\Type(['type' => 'integer'])),
                        'type' => new Assert\Required(new Assert\Choice(['MCQ'])),
                        'label' => new Assert\Required(new Assert\NotBlank()),
                        'choices' => new Assert\Optional([
                            new Assert\Type(["type" => "array"]),
                            new Assert\All([
                                new Assert\Collection([
                                    'fields' => [
                                        'isCorrect' => new Assert\Required(new Assert\Type(['type' => 'boolean'])),
                                        'label' => new Assert\Required(new Assert\NotBlank())
                                    ]
                                ])
                            ])
                        ])
                    ]
                ]),
                new MCQ(),
            ])
            ]
        );
    }
}
