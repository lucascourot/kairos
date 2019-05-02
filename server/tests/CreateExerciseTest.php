<?php

namespace App\Tests;

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

        $response->getContent();

        $this->exerciseId = $response->toArray()['id'];

        $this->assertSame(201, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        $this->client->request('DELETE', '/api/exercises/'.$this->exerciseId);
    }
}
