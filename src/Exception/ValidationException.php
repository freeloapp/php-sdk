<?php

declare(strict_types=1);

namespace Freelo\Sdk\Exception;

/**
 * Exception thrown when validation fails
 */
class ValidationException extends ApiException
{
    /**
     * @var array<string, string[]>
     */
    private array $errors = [];

    /**
     * @param array<string, string[]> $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @return array<string, string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
