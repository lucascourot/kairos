<?php

namespace App\Validator\Constraints;

use App\Entity\Exercise;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @Annotation
 */
final class MCQValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof MCQ) {
            throw new UnexpectedTypeException($constraint, MCQ::class);
        }

        $question = $value;

        if (isset($question['type'], $question['choices']) && Exercise::TYPE_MCQ === strtoupper($question['type'])) {
            $missingField = false;

            foreach ($question['choices'] as $choice) {
                if (!isset($choice['isCorrect'])) {
                    $this->context->buildViolation($constraint->isCorrectFieldMissing)->addViolation();
                    $missingField = true;
                }
            }

            if ($missingField) {return;}

            $isValidQuestion = false;

            foreach ($question['choices'] as $choice) {
                if (($choice['isCorrect'] ?? false) === true) {
                    $isValidQuestion = true;
                }
            }

            if ($isValidQuestion === false) {
                $this->context->buildViolation($constraint->validQuestionRequired)->addViolation();
            }
        }
    }
}
