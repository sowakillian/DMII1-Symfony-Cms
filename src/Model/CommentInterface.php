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
    public function getContent(): ?string;

    public function setContent(?string $content): void;
}