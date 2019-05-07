<?php

namespace App\Tests;

use App\Entity\Exercise;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CreateExerciseTest extends KernelTestCase
{
    /** @var HttpClientInterface */
    private $client;

    /** @var int */
    private $exerciseId;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->client = HttpClient::create([
            'base_uri' => $kernel->getContainer()->getParameter('api_base_uri'),
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);
    }

    public function testShouldCreateExerciseWithMCQ()
    {
        // When
        $response = $this->client->request('POST', '/api/exercises', [
            'json' => [
                'name' => 'test - exercise with MCQ',
                'questions' => [
                    [
                        'position' => 1,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'What\'s the sun color?',
                        'choices' => [
                            ['isCorrect' => true, 'label' => 'yellow'],
                            ['isCorrect' => false, 'label' => 'blue'],
                            ['isCorrect' => false, 'label' => 'green'],
                            ['isCorrect' => false, 'label' => 'pink'],
                        ],
                    ],
                ],
            ],
        ]);

        $this->exerciseId = $response->toArray()['id'];

        // Then
        $this->assertSame(201, $response->getStatusCode());
    }

    public function testShouldNotCreateExerciseWithMCQButNoGoodChoice()
    {
        // When
        $response = $this->client->request('POST', '/api/exercises', [
            'json' => [
                'name' => 'test - exercise with MCQ but no good choice',
                'questions' => [
                    [
                        'position' => 1,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'Where does the rain come from?',
                        'choices' => [
                            ['isCorrect' => false, 'label' => 'the sun'],
                            ['isCorrect' => false, 'label' => 'the birds'],
                            ['isCorrect' => false, 'label' => 'the roof'],
                            ['isCorrect' => false, 'label' => 'the sky'],
                        ],
                    ],
                    [
                        'position' => 2,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'Where does rice come from?',
                        'choices' => [
                            ['isCorrect' => true, 'label' => 'china'],
                            ['isCorrect' => false, 'label' => 'russia'],
                            ['isCorrect' => false, 'label' => 'france'],
                            ['isCorrect' => false, 'label' => 'usa'],
                        ],
                    ],
                ],
            ],
        ]);

        // Then
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testShouldValidateExerciseCreation()
    {
        // When
        $response = $this->client->request('POST', '/api/exercises', [
            'json' => [
                'name' => 'test - invalid exercise',
                'questions' => [
                    [
                        'position' => 1,
                        'type' => '',
                        'label' => '',
                        'choices' => [],
                    ],
                ],
            ],
        ]);

        // Then
        $this->assertSame(400, $response->getStatusCode());
        $errorMessages = array_column($response->toArray(false)['violations'], 'message');

        $this->assertEquals([
            'The value you selected is not a valid choice.',
            'This value should not be blank.',
        ], $errorMessages);
    }

    public function testShouldHaveValidChoices()
    {
        // When
        $response = $this->client->request('POST', '/api/exercises', [
            'json' => [
                'name' => 'test - exercise questions should have valid choices',
                'questions' => [
                    [
                        'position' => 1,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'What is the correct answer?',
                        'choices' => [
                            ['isCorrect' => false],
                            ['isCorrect' => true, 'label' => 'this one'],
                            ['label' => 'not this one'],
                            [],
                        ],
                    ],
                ],
            ],
        ]);

        // Then
        $this->assertSame(400, $response->getStatusCode());
        $errorMessages = array_column($response->toArray(false)['violations'], 'message');

        $this->assertEquals([
            'This field is missing.',
            'This field is missing.',
            'This field is missing.',
            'This field is missing.'
        ], $errorMessages);
    }

    public function testShouldAddPositionNumberOnQuestions()
    {
        // When
        $response = $this->client->request('POST', '/api/exercises', [
            'json' => [
                'name' => 'test - questions should have a position number',
                'questions' => [
                    [
                        'position' => 1,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'Where does the rain come from?',
                        'choices' => [
                            ['isCorrect' => false, 'label' => 'the sun'],
                            ['isCorrect' => false, 'label' => 'the birds'],
                            ['isCorrect' => false, 'label' => 'the roof'],
                            ['isCorrect' => true, 'label' => 'the sky'],
                        ],
                    ],
                    [
                        'position' => 2,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'Where does rice come from?',
                        'choices' => [
                            ['isCorrect' => true, 'label' => 'china'],
                            ['isCorrect' => false, 'label' => 'russia'],
                            ['isCorrect' => false, 'label' => 'france'],
                            ['isCorrect' => false, 'label' => 'usa'],
                        ],
                    ],
                    [
                        'position' => 3,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'What\'s the sun color?',
                        'choices' => [
                            ['isCorrect' => true, 'label' => 'yellow'],
                            ['isCorrect' => false, 'label' => 'blue'],
                            ['isCorrect' => false, 'label' => 'green'],
                            ['isCorrect' => false, 'label' => 'pink'],
                        ],
                    ],
                ],
            ]
        ]);

        // Then
        $this->assertSame(201, $response->getStatusCode());
    }

    /**
     * @dataProvider invalidQuestionPositionProvider
     */
    public function testShouldHavePositionThatFollowEachOther($position1, $position2, $position3)
    {
        $response = $this->client->request('POST', '/api/exercises', [
            'json' => [
                'name' => 'test - questions should have a positions that follow each other',
                'questions' => [
                    [
                        'position' => $position1,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'What\'s the sun color?',
                        'choices' => [
                            ['isCorrect' => true, 'label' => 'yellow'],
                            ['isCorrect' => false, 'label' => 'blue'],
                            ['isCorrect' => false, 'label' => 'green'],
                            ['isCorrect' => false, 'label' => 'pink'],
                        ],
                    ],
                    [
                        'position' => $position2,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'Where does the rain come from?',
                        'choices' => [
                            ['isCorrect' => false, 'label' => 'the sun'],
                            ['isCorrect' => false, 'label' => 'the birds'],
                            ['isCorrect' => false, 'label' => 'the roof'],
                            ['isCorrect' => true, 'label' => 'the sky'],
                        ],
                    ],
                    [
                        'position' => $position3,
                        'type' => Exercise::TYPE_MCQ,
                        'label' => 'Where does rice come from?',
                        'choices' => [
                            ['isCorrect' => true, 'label' => 'china'],
                            ['isCorrect' => false, 'label' => 'russia'],
                            ['isCorrect' => false, 'label' => 'france'],
                            ['isCorrect' => false, 'label' => 'usa'],
                        ],
                    ],
                ],
            ]
        ]);

        // Then
        $this->assertSame(400, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        if ($this->exerciseId) {
            $this->client->request('DELETE', '/api/exercises/' . $this->exerciseId);
        }
    }

    public function invalidQuestionPositionProvider()
    {
        yield [2, 3, 4]; // don't begin with 1
        yield [1, 3, 2]; // begins with 1, but don't follow each other
        yield [1, 2 ,2]; // begins with 1, but has duplicates
    }

    public function testShouldCreateOpenEndedQuestion()
    {
        //When
        $response = $this->client->request('POST', 'api/exercises', [
            'json' => [
                'name' => 'test - exercise with free answer',
                'questions' => [
                    [
                        'position' => 1,
                        'type' => Exercise::TYPE_OPEN_ENDED,
                        'label' => 'What color can be the sun ?',
                        'choices' => [
                            ['label' => 'yellow'],
                            ['label' => 'orange'],
                            ['label' => 'red'],
                        ]
                    ]
                ]
            ]
        ]);

        $this->exerciseId = $response->toArray()['id'];

        //Then
        $this->assertSame(201, $response->getStatusCode());
    }

    public function testShouldNotCreateOpenEndedQuestionWithSomeExtraChoiceFields()
    {
        //When
        $response = $this->client->request('POST', 'api/exercises', [
            'json' => [
                'name' => 'test - exercise with free answer',
                'questions' => [
                    [
                        'position' => 1,
                        'type' => Exercise::TYPE_OPEN_ENDED,
                        'label' => 'What color can be the sun ?',
                        'choices' => [
                            ['isCorrect' => true, 'label' => 'yellow'],
                            ['isCorrect' => true, 'label' => 'orange'],
                            ['isCorrect' => true, 'label' => 'red'],
                        ]
                    ]
                ]
            ]
        ]);

        //Then
        $this->assertSame(400, $response->getStatusCode());
    }
}
