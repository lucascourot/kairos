<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OpenEnded extends Constraint
{
    public $onlyLabelField = 'For Open ended question, choices should only implement "label" field';
}
