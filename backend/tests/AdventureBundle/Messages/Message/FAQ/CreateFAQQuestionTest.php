<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Tests\AdventureBundle\Messages\Message\FAQ;

use Tests\FoundationTestCase;
use Divante\Bundle\AdventureBundle\Message\FAQ\CreateFAQQuestion;

class CreateFAQQuestionTest extends FoundationTestCase
{
    public function testQuestionPlReturnedCorrectly() : void
    {
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new CreateFAQQuestion(
            $employeeId,
            $categoryId,
            $questionPl,
            $questionEn,
            $answerPl,
            $answerEn
        );
        $this->assertEquals($questionPl, $message->getQuestionPl());
    }

    public function testQuestionEnReturnedCorrectly() : void
    {
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new CreateFAQQuestion(
            $employeeId,
            $categoryId,
            $questionPl,
            $questionEn,
            $answerPl,
            $answerEn
        );
        $this->assertEquals($questionEn, $message->getQuestionEn());
    }

    public function testAnswerPlReturnedCorrectly() : void
    {
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new CreateFAQQuestion(
            $employeeId,
            $categoryId,
            $questionPl,
            $questionEn,
            $answerPl,
            $answerEn
        );
        $this->assertEquals($answerPl, $message->getAnswerPl());
    }

    public function testAnswerEnReturnedCorrectly() : void
    {
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new CreateFAQQuestion(
            $employeeId,
            $categoryId,
            $questionPl,
            $questionEn,
            $answerPl,
            $answerEn
        );
        $this->assertEquals($answerEn, $message->getAnswerEn());
    }
}
