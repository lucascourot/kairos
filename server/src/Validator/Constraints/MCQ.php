<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MCQ extends Constraint
{
    public $validQuestionRequired = 'The MCQ must have at least one valid choice.';
}
