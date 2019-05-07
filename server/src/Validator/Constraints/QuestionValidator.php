<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @Annotation
 */
final class QuestionValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Question) {
            throw new UnexpectedTypeException($constraint, Question::class);
        }

        $questions = $value;

        if (($questions[0]['position'] ?? null) !== 1) {
            $this->context->buildViolation($constraint->shouldStartAtPositionOne)->addViolation();
        }

        $expectedPosition = 1;
        foreach ($questions as $question) {
            if (($question['position'] ?? null) !== $expectedPosition) {
                $this->context->buildViolation($constraint->shouldBeFollowingEachOther)->addViolation();
            }

            $expectedPosition++;
        }
    }
}
