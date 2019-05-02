<?php

namespace App\Controller;

use App\Entity\Exercise;
use Symfony\Bridge\Doctrine\RegistryInterface;

final class CreateExerciseController
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function __invoke(Exercise $data): Exercise
    {
        $em = $this->doctrine->getEntityManagerForClass(Exercise::class);
        $em->persist($data);
        $em->flush();

        if (!$data->areAllMCQValid()) {
            throw new \InvalidArgumentException('A choice must be selected.');
        }

        return $data;
    }
}
