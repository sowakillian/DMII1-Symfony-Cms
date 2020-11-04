<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Interface CommentInterface
 *
 * @author Jérôme Fath
 */
interface CommentInterface extends StatusAwareInterface
{
    const STATUS_WAITING = 'waiting';
    const STATUS_VALIDATED = 'validated';

    public function getContent(): ?string;

    public function setContent(?string $content): void;
}