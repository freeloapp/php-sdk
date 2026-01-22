<?php

declare(strict_types=1);

namespace Freelo\Sdk\Tests\Fixtures;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base test case with fixture loading capabilities
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Load a JSON fixture file and return as array
     *
     * @param string $name Fixture name (e.g., 'projects-list' or 'project-detail')
     * @return array<string, mixed>
     */
    protected function loadFixture(string $name): array
    {
        $path = __DIR__ . '/responses/' . $name . '.json';

        if (!file_exists($path)) {
            throw new \RuntimeException("Fixture file not found: {$path}");
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException("Failed to read fixture file: {$path}");
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON in fixture file: {$path} - " . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Load a JSON fixture file and return as raw JSON string
     *
     * @param string $name Fixture name
     * @return string
     */
    protected function loadFixtureRaw(string $name): string
    {
        $path = __DIR__ . '/responses/' . $name . '.json';

        if (!file_exists($path)) {
            throw new \RuntimeException("Fixture file not found: {$path}");
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException("Failed to read fixture file: {$path}");
        }

        return $content;
    }

    /**
     * Get the path to a fixture file
     *
     * @param string $name Fixture name
     * @return string
     */
    protected function getFixturePath(string $name): string
    {
        return __DIR__ . '/responses/' . $name . '.json';
    }
}
