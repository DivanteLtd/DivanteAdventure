<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Tests\AdventureBundle\Messages\Message\FAQ;

use Divante\Bundle\AdventureBundle\Message\FAQ\UpdateFAQQuestion;
use Tests\FoundationTestCase;

class UpdateFAQQuestionTest extends FoundationTestCase
{

    public function testIdReturnedCorrectly() : void
    {
        $id = rand(0, 10000);
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new UpdateFAQQuestion(
            $id,
            $employeeId,
            $categoryId,
            $questionPl,
            $questionEn,
            $answerPl,
            $answerEn
        );
        $this->assertEquals($id, $message->getId());
    }

    public function testEmployeeIdReturnedCorrectly() : void
    {
        $id = rand(0, 10000);
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new UpdateFAQQuestion(
            $id,
            $employeeId,
            $categoryId,
            $questionPl,
            $questionEn,
            $answerPl,
            $answerEn
        );
        $this->assertEquals($employeeId, $message->getEmployeeId());
    }

    public function testCategoryIdReturnedCorrectly() : void
    {
        $id = rand(0, 10000);
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new UpdateFAQQuestion(
            $id,
            $employeeId,
            $categoryId,
            $questionPl,
            $questionEn,
            $answerPl,
            $answerEn
        );
        $this->assertEquals($categoryId, $message->getCategoryId());
    }

    public function testQuestionPlReturnedCorrectly() : void
    {
        $id = rand(0, 10000);
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new UpdateFAQQuestion(
            $id,
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
        $id = rand(0, 10000);
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new UpdateFAQQuestion(
            $id,
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
        $id = rand(0, 10000);
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new UpdateFAQQuestion(
            $id,
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
        $id = rand(0, 10000);
        $employeeId = rand(0, 10000);
        $categoryId = rand(0, 10000);
        $questionPl = "QuestionPl".rand(0, 10000);
        $questionEn = "QuestionEn".rand(0, 10000);
        $answerPl = "AnswerPl".rand(0, 10000);
        $answerEn = "AnswerEn".rand(0, 10000);
        $message = new UpdateFAQQuestion(
            $id,
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
