<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class QuestionPositions extends Constraint
{
    public $shouldStartAtPositionOne = 'The position of the first question should always be 1.';

    public $shouldBeFollowingEachOther = 'The question positions should follow each other.';
}
