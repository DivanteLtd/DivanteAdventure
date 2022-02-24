<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Dashboard;

use Divante\Bundle\AdventureBundle\Entity\News;
use Divante\Bundle\AdventureBundle\Message\Dashboard\EditPost;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class EditPostHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param EditPost $editPost
     * @throws Exception
     */
    public function __invoke(EditPost $editPost) : void
    {
        $em = $this->em;
        /** @var News|null $news */
        $news = $em->getRepository(News::class)->find($editPost->getId());
        if (is_null($news)) {
            throw new Exception("News not found");
        }
        if (is_null($editPost->getTitle()) && is_null($editPost->getBanner())) {
            throw new Exception("Title and banner are not required, but at least one of them must be supplied.");
        }
        $news
            ->setTitle($editPost->getTitle())
            ->setBanner($editPost->getBanner())
            ->setDescription($editPost->getDescription())
            ->setType($editPost->getType())
            ->setUpdatedAt();

        try {
            $em->persist($news);
            $em->flush();
        } catch (Exception $e) {
            throw new Exception('Edit post failed', 0, $e);
        }
    }
}
