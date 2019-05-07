<?php

namespace App\Validator\Constraints;

use App\Entity\Exercise;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @Annotation
 */
final class OpenEndedValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof OpenEnded) {
            throw new UnexpectedTypeException($constraint, MCQ::class);
        }

        $question = $value;

        if (isset($question['type'], $question['choices']) && Exercise::TYPE_OPEN === strtoupper($question['type'])) {
            $isValidQuestion = true;

            foreach ($question['choices'] as $choice) {
                $keys = array_keys($choice);
                if ($keys !== ['label']) {
                    $isValidQuestion = false;
                }
            }

            if ($isValidQuestion === false) {
                $this->context->buildViolation($constraint->onlyLabelField)->addViolation();
            }
        }
    }
}
