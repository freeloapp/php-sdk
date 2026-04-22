<?php

/**
 * OpenAPI Endpoints Digest Generator
 *
 * Reads the OpenAPI spec and emits a Markdown digest of every endpoint
 * (summary, description, parameters, request body, responses) into
 * docs/ENDPOINTS.md — suitable as context for LLMs and as human reference.
 *
 * Usage:
 *   php scripts/generate-endpoints-digest.php
 *   composer generate
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$specPath = __DIR__ . '/../.openapi/freelo-api.yaml';
$outputPath = __DIR__ . '/../docs/ENDPOINTS.md';

if (!file_exists($specPath)) {
    fwrite(STDERR, "OpenAPI spec not found at {$specPath}\n");
    fwrite(STDERR, "Run `make spec` or download it manually first.\n");
    exit(1);
}

$spec = Yaml::parseFile($specPath);
$paths = $spec['paths'] ?? [];
$info = $spec['info'] ?? [];
$sharedParameters = $spec['components']['parameters'] ?? [];

if (empty($paths)) {
    fwrite(STDERR, "No paths found in OpenAPI spec\n");
    exit(1);
}

$outputDir = dirname($outputPath);
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$httpMethods = ['get', 'post', 'put', 'patch', 'delete', 'options', 'head'];

/** @var array<string, array<int, array<string, mixed>>> $byTag */
$byTag = [];
$untagged = [];
$totalOps = 0;

foreach ($paths as $path => $pathItem) {
    if (!is_array($pathItem)) {
        continue;
    }
    foreach ($httpMethods as $method) {
        if (!isset($pathItem[$method]) || !is_array($pathItem[$method])) {
            continue;
        }
        $op = $pathItem[$method];
        $op['_path'] = $path;
        $op['_method'] = strtoupper($method);

        $tags = $op['tags'] ?? [];
        if (empty($tags)) {
            $untagged[] = $op;
        } else {
            foreach ($tags as $tag) {
                $byTag[$tag][] = $op;
            }
        }
        $totalOps++;
    }
}

ksort($byTag);
if (!empty($untagged)) {
    $byTag['Untagged'] = $untagged;
}

$out = [];
$out[] = '<!-- @generated Auto-generated from OpenAPI spec — do not edit manually. -->';
$out[] = '<!-- @see scripts/generate-endpoints-digest.php -->';
$out[] = '';
$out[] = '# Freelo API Endpoints';
$out[] = '';

if (!empty($info['title']) || !empty($info['version'])) {
    $headerBits = [];
    if (!empty($info['title'])) {
        $headerBits[] = $info['title'];
    }
    if (!empty($info['version'])) {
        $headerBits[] = 'v' . $info['version'];
    }
    $out[] = '**' . implode(' — ', $headerBits) . '**';
    $out[] = '';
}

$out[] = 'This digest exists to give LLMs and humans fast access to endpoint semantics '
    . '(use cases, behavior notes, side effects) without parsing the full spec. '
    . 'Regenerated from `.openapi/freelo-api.yaml` on every `composer generate`.';
$out[] = '';
$out[] = "Total endpoints: **{$totalOps}** across " . count($byTag) . ' tag(s).';
$out[] = '';

$out[] = '## Table of contents';
$out[] = '';
foreach ($byTag as $tag => $ops) {
    $anchor = tagToAnchor($tag);
    $count = count($ops);
    $out[] = "- [{$tag}](#{$anchor}) — {$count} endpoint" . ($count === 1 ? '' : 's');
}
$out[] = '';

foreach ($byTag as $tag => $ops) {
    $out[] = '## ' . $tag;
    $out[] = '';

    usort($ops, function (array $a, array $b): int {
        return strcmp($a['_path'] . ' ' . $a['_method'], $b['_path'] . ' ' . $b['_method']);
    });

    foreach ($ops as $op) {
        $out[] = '### `' . $op['_method'] . ' ' . $op['_path'] . '`';
        $out[] = '';

        if (!empty($op['summary'])) {
            $out[] = '**' . trim($op['summary']) . '**';
            $out[] = '';
        }

        if (!empty($op['operationId'])) {
            $out[] = '`operationId`: `' . $op['operationId'] . '`';
            $out[] = '';
        }

        if (!empty($op['description'])) {
            $out[] = rtrim($op['description']);
            $out[] = '';
        }

        $parameters = $op['parameters'] ?? [];
        if (!empty($parameters)) {
            $out[] = '**Parameters:**';
            $out[] = '';
            foreach ($parameters as $param) {
                $resolved = resolveParameter($param, $sharedParameters);
                if ($resolved === null) {
                    continue;
                }
                $out[] = formatParameter($resolved);
            }
            $out[] = '';
        }

        if (!empty($op['requestBody'])) {
            $out[] = '**Request body:**';
            $out[] = '';
            $out[] = formatRequestBody($op['requestBody']);
            $out[] = '';
        }

        if (!empty($op['responses'])) {
            $out[] = '**Responses:**';
            $out[] = '';
            foreach ($op['responses'] as $code => $response) {
                $desc = '';
                if (is_array($response) && !empty($response['description'])) {
                    $desc = ' — ' . trim(preg_replace('/\s+/', ' ', $response['description']) ?? '');
                }
                $schemaRef = extractResponseSchemaRef($response);
                $refNote = $schemaRef !== null ? " _(schema: `{$schemaRef}`)_" : '';
                $out[] = "- `{$code}`{$desc}{$refNote}";
            }
            $out[] = '';
        }

        $out[] = '---';
        $out[] = '';
    }
}

$markdown = implode("\n", $out);
// Collapse trailing blank lines.
$markdown = rtrim($markdown) . "\n";

file_put_contents($outputPath, $markdown);
echo "Generated endpoints digest ({$totalOps} endpoints, " . count($byTag) . " tags) in docs/ENDPOINTS.md\n";

// --- Helpers ---

function tagToAnchor(string $tag): string
{
    $anchor = strtolower($tag);
    $anchor = preg_replace('/[^a-z0-9\- ]/', '', $anchor) ?? '';
    $anchor = str_replace(' ', '-', trim($anchor));
    return $anchor;
}

/**
 * @param array<string, mixed> $param
 * @param array<string, mixed> $sharedParameters
 * @return array<string, mixed>|null
 */
function resolveParameter(array $param, array $sharedParameters): ?array
{
    if (isset($param['$ref'])) {
        $parts = explode('/', (string) $param['$ref']);
        $name = end($parts);
        if (is_string($name) && isset($sharedParameters[$name]) && is_array($sharedParameters[$name])) {
            return $sharedParameters[$name];
        }
        return null;
    }
    return $param;
}

/**
 * @param array<string, mixed> $param
 */
function formatParameter(array $param): string
{
    $name = $param['name'] ?? '?';
    $in = $param['in'] ?? '?';
    $required = !empty($param['required']) ? ', required' : '';
    $schema = $param['schema'] ?? [];
    $type = is_array($schema) ? describeSchema($schema) : '';
    $desc = !empty($param['description'])
        ? ' — ' . trim(preg_replace('/\s+/', ' ', (string) $param['description']) ?? '')
        : '';
    $typeFragment = $type !== '' ? " ({$type})" : '';
    return "- `{$name}` [{$in}{$required}]{$typeFragment}{$desc}";
}

/**
 * @param array<string, mixed> $schema
 */
function describeSchema(array $schema): string
{
    if (isset($schema['$ref'])) {
        $parts = explode('/', (string) $schema['$ref']);
        return (string) end($parts);
    }
    $type = $schema['type'] ?? '';
    if ($type === 'array' && isset($schema['items']) && is_array($schema['items'])) {
        return 'array<' . describeSchema($schema['items']) . '>';
    }
    if (isset($schema['enum']) && is_array($schema['enum'])) {
        $values = array_map(fn($v) => is_scalar($v) ? (string) $v : gettype($v), $schema['enum']);
        return $type . ' enum: ' . implode('|', $values);
    }
    if (!empty($schema['format'])) {
        return $type . '<' . $schema['format'] . '>';
    }
    return is_string($type) ? $type : '';
}

/**
 * @param array<string, mixed> $requestBody
 */
function formatRequestBody(array $requestBody): string
{
    $required = !empty($requestBody['required']) ? ' (required)' : '';
    $lines = ["_Request body{$required}_"];

    $content = $requestBody['content'] ?? [];
    if (!is_array($content)) {
        return implode("\n", $lines);
    }

    foreach ($content as $mediaType => $mediaObj) {
        if (!is_array($mediaObj)) {
            continue;
        }
        $lines[] = '';
        $lines[] = '- Content-Type: `' . $mediaType . '`';

        $schema = $mediaObj['schema'] ?? null;
        if (is_array($schema)) {
            $schemaDesc = describeSchema($schema);
            if ($schemaDesc !== '') {
                $lines[] = '- Schema: `' . $schemaDesc . '`';
            }
            $props = $schema['properties'] ?? [];
            $requiredProps = $schema['required'] ?? [];
            if (is_array($props) && !empty($props)) {
                $lines[] = '- Properties:';
                foreach ($props as $propName => $propSchema) {
                    if (!is_array($propSchema)) {
                        continue;
                    }
                    $isReq = is_array($requiredProps) && in_array($propName, $requiredProps, true);
                    $reqMark = $isReq ? ' **required**' : '';
                    $propType = describeSchema($propSchema);
                    $propDesc = !empty($propSchema['description'])
                        ? ' — ' . trim(preg_replace('/\s+/', ' ', (string) $propSchema['description']) ?? '')
                        : '';
                    $lines[] = "    - `{$propName}`{$reqMark} ({$propType}){$propDesc}";
                }
            }
        }
    }

    return implode("\n", $lines);
}

/**
 * @param mixed $response
 */
function extractResponseSchemaRef($response): ?string
{
    if (!is_array($response)) {
        return null;
    }
    $content = $response['content'] ?? [];
    if (!is_array($content)) {
        return null;
    }
    foreach ($content as $mediaObj) {
        if (!is_array($mediaObj)) {
            continue;
        }
        $schema = $mediaObj['schema'] ?? null;
        if (!is_array($schema)) {
            continue;
        }
        if (isset($schema['$ref'])) {
            $parts = explode('/', (string) $schema['$ref']);
            return (string) end($parts);
        }
        if (($schema['type'] ?? '') === 'array' && isset($schema['items']['$ref'])) {
            $parts = explode('/', (string) $schema['items']['$ref']);
            return 'array<' . end($parts) . '>';
        }
    }
    return null;
}
