<?php

namespace App\Tests;

use App\Validator\Constraints\QuestionsMinimalProperties;
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
        $this->client = HttpClient::create([
            'base_uri' => 'http://127.0.0.1:8000',
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
                        'type' => 'MCQ',
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
                        'type' => 'MCQ',
                        'label' => 'Where does the rain come from?',
                        'choices' => [
                            ['isCorrect' => false, 'label' => 'the sun'],
                            ['isCorrect' => false, 'label' => 'the birds'],
                            ['isCorrect' => false, 'label' => 'the roof'],
                            ['isCorrect' => false, 'label' => 'the sky'],
                        ],
                    ],
                    [
                        'type' => 'MCQ',
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
                        'type' => '',
                        'label' => '',
                    ],
                ],
            ],
        ]);

        // Then
        $this->assertSame(400, $response->getStatusCode());
        $errorMessages = array_column($response->toArray(false)['violations'], 'message');
        $this->assertEquals([
            'The "type" field should not be blank.',
            'The "label" field should not be blank.',
        ], $errorMessages);
    }

    protected function tearDown(): void
    {
        if ($this->exerciseId) {
            $this->client->request('DELETE', '/api/exercises/'.$this->exerciseId);
        }
    }
}
