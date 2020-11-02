<?php

namespace App\Model;

/**
 * Class Comment
 *
 * @author Amélie Mouillac
 */
class Comment implements CommentInterface
{
    public function getContent(): ?string {
        return 'titi';
    }

    public function setContent(?string $content): void {}

    public function getTitle(): ?string {
        return 'toto';
    }

    public function setTitle(?string $title) {}

    public function getStatus(): ?string {}

    public function setStatus(string $status): void {}
}