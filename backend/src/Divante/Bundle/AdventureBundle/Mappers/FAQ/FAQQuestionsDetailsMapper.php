<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Mappers\FAQ;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQQuestion;
use Divante\Bundle\AdventureBundle\Mappers\AbstractDoctrineAwareMapper;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;

class FAQQuestionsDetailsMapper extends AbstractDoctrineAwareMapper implements Mapper
{
    /**
     * @param FAQCategory[] $categories
     * @return array<int,array<string,mixed>>
     * @throws \Exception
     */
    public function mapEntity($categories): array
    {
        $json = [];
        /** @var FAQCategory $category */
        foreach ($categories as $category) {
            $em = $this->getObjectManager();
            $questions = $em->getRepository(FAQQuestion::class)
                ->findBy(['fAQCategory' => $category]);
            $tmp = [];
            /** @var FAQQuestion $question */
            foreach ($questions as $question) {
                $tmp[] = [
                    'id' => $question->getId(),
                    'author' => [
                        'id' => $question->getEmployee()->getId(),
                        'name' => $question->getEmployee()->getName(),
                        'lastName' => $question->getEmployee()->getLastName(),
                        'email' => $question->getEmployee()->getEmail(),
                        'photo' => $question->getEmployee()->getPhoto(),
                    ],
                    'questionPl' => $question->getQuestionPl(),
                    'questionEn' => $question->getQuestionEn(),
                    'answerPl' => $question->getAnswerPl(),
                    'answerEn' => $question->getAnswerEn(),
                    'createdAt' => $question->getCreatedAt()->format('Y-m-d H:i:s'),
                ];
            }
                $json[] = [
                    'id' => $category->getId(),
                    'namePl' => $category->getNamePl(),
                    'nameEn' => $category->getNameEn(),
                    'questions' => $tmp,
                ];
        }
        return $json;
    }
}
