<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidAnswers extends Constraint
{
    public $allCorrectRequired = 'Only correct answers are allowed for Free text exercises';
}
