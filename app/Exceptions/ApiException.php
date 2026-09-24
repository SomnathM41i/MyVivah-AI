<?php

namespace App\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiException extends RuntimeException
{
    /**
     * Stable machine-readable error code (phase-3a-api-integration-plan.md §11).
     */
    public function __construct(
        protected readonly string $errorCode,
        string $message,
        protected readonly int $status = Response::HTTP_BAD_REQUEST,
        private readonly ?array $errors = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function errors(): ?array
    {
        return $this->errors;
    }
}
