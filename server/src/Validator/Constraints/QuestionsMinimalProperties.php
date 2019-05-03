<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class QuestionsMinimalProperties extends Constraint
{
    public $minimalPropertiesRequired = 'The questions must have the minimal properties required ("type", "label").';

    public $typeFieldRequired = 'The "type" field should not be blank.';

    public $labelFieldRequired = 'The "label" field should not be blank.';

    public $validQuestionRequired = 'The MCQ must have at least one valid answer.';
}
