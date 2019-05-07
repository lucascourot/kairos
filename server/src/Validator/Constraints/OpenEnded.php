<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OpenEnded extends Constraint
{
    public $onlyLabelField = 'For Open ended exercise, choices should only implement "label" field';
}
