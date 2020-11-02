<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Interface StatusAwareInterface.
 *
 * @author Jérôme Fath
 */
interface StatusAwareInterface
{
    public function getStatus(): ?string;

    public function setStatus(string $status): void;
}
