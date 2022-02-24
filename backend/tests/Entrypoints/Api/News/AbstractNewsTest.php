<?php

namespace Tests\Entrypoints\Api\News;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\News;
use Tests\Entrypoints\AbstractEntrypointTest;

abstract class AbstractNewsTest extends AbstractEntrypointTest
{
    public function generateNews(Employee $author) : News
    {
        $news = new News();
        $news->setAuthor($author)
            ->setDescription("Text".rand(0, 10000))
            ->setTitle("Title".rand(0, 10000))
            ->setType(News::TYPE_MARKDOWN)
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($news);
        return $news;
    }
}