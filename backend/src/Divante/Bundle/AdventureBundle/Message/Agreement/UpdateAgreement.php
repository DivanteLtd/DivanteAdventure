<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 04.01.19
 * Time: 10:03
 */

namespace Divante\Bundle\AdventureBundle\Message\Agreement;

use Divante\Bundle\AdventureBundle\Entity\Agreement;

class UpdateAgreement extends AbstractAgreementMessage
{
    private Agreement $entry;

    /** @inheritDoc */
    public function __construct(
        Agreement $entry,
        string $namePl,
        string $nameEn,
        string $descriptionPl,
        string $descriptionEn,
        bool $required,
        int $priority,
        array $contracts,
        array $attachments,
        int $type
    ) {
        parent::__construct(
            $namePl,
            $nameEn,
            $descriptionPl,
            $descriptionEn,
            $required,
            $priority,
            $contracts,
            $attachments,
            $type
        );
        $this->entry = $entry;
    }

    public function getEntry(): Agreement
    {
        return $this->entry;
    }
}
