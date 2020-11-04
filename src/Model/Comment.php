<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Comment
 *
 * @author Amélie Mouillac
 */
class Comment implements CommentInterface
{
    protected $title;
    protected $content;

    protected $status = CommentInterface::STATUS_WAITING;

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function getStatus(): ?string {}

    public function setStatus(string $status): void {}

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        //$metadata->addPropertyConstraint('title', new Assert\Type('integer'));
    }
    
}