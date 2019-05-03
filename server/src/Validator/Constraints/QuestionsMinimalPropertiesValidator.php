<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @Annotation
 */
final class QuestionsMinimalPropertiesValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof QuestionsMinimalProperties) {
            throw new UnexpectedTypeException($constraint, QuestionsMinimalProperties::class);
        }

        if (null === $value) {
            return;
        }

        $questions = $value;

        foreach ($questions as $question) {
            if (!empty(array_diff(['type', 'label'], array_keys($question)))) {
                $this->context->buildViolation($constraint->minimalPropertiesRequired)->addViolation();
            }

            if ('' === $question['type']) {
                $this->context->buildViolation($constraint->typeFieldRequired)->addViolation();
            }

            if ('' === $question['label']) {
                $this->context->buildViolation($constraint->labelFieldRequired)->addViolation();
            }

            if ('MCQ' === strtoupper($question['type'])) {
                $isValidQuestion = false;

                foreach ($question['choices'] as $choice) {
                    if ($choice['isCorrect'] === true) {
                        $isValidQuestion = true;
                    }

                    if ($isValidQuestion === false) {
                        $this->context->buildViolation($constraint->validQuestionRequired)->addViolation();
                    }
                }
            }
        }
    }
}
