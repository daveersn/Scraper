<?php

namespace App\Exceptions;

use Throwable;

final class SubitoItemNormalizationException extends \RuntimeException
{
    public function __construct(private readonly array $item, ?Throwable $previous = null)
    {
        parent::__construct(
            message: 'Failed to normalize Subito item.',
            previous: $previous
        );
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return ['item' => $this->item];
    }
}
