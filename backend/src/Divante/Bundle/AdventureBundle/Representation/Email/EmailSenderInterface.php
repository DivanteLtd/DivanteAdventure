<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Representation\Email;

/**
 * Interface EmailInterface
 */
interface EmailSenderInterface
{
    /**
     * @param string|string[] $to
     * @param string $Cc
     * @param string $subject
     * @param array<string,mixed> $params
     * @param string $template
     * @param string|null $attachmentPath
     *
     * @return int
     */
    public function sendMessage(
        $to,
        ?string $Cc,
        string $subject,
        array $params,
        string $template,
        string $attachmentPath = null
    ) : int;

    /**
     * @param string|string[] $to
     * @param string $Cc
     * @param string $subject
     * @param array<string,mixed> $params
     * @param string $template
     * @param \Swift_Attachment[] $attachments
     *
     * @return int
     */
    public function sendMessageWithAttachments(
        $to,
        ?string $Cc,
        string $subject,
        array $params,
        string $template,
        array $attachments
    ) : int;
}
