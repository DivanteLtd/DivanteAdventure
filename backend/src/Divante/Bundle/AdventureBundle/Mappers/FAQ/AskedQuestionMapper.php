<?php

namespace Divante\Bundle\AdventureBundle\Mappers\FAQ;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQAskedQuestion;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;

class AskedQuestionMapper implements Mapper
{

    /**
     * @param FAQAskedQuestion $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            'id' => $entity->getId(),
            'question' => $entity->getQuestion(),
            'language' => $entity->getLanguage() ?? 'en',
            'questioner' => [
                'id' => $entity->getQuestioner()->getId(),
                'name' => $entity->getQuestioner()->getName(),
                'lastName' => $entity->getQuestioner()->getLastName(),
                'photo' => $entity->getQuestioner()->getPhoto(),
            ],
            'category' => [
                'id' => $entity->getCategory()->getId(),
                'namePl' => $entity->getCategory()->getNamePl(),
                'nameEn' => $entity->getCategory()->getNameEn(),
            ],
            'createdAt' => $entity->getCreatedAt(),
            'updatedAt' => $entity->getUpdatedAt(),
        ];
    }

    /**
     * @param FAQAskedQuestion $question
     * @return array<string,mixed>
     */
    public function __invoke(FAQAskedQuestion $question) : array
    {
        return $this->mapEntity($question);
    }
}
