<?php

/**
 * OpenAPI Model Generator for Freelo PHP SDK
 *
 * Parses the OpenAPI spec and generates PHP model classes into src/Generated/Model/
 * following the existing fromArray()/toArray() pattern with readonly constructor properties.
 *
 * Usage:
 *   php scripts/generate-models.php
 *   composer generate
 *   composer generate:check  (generates + verifies no diff)
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$specPath = __DIR__ . '/../.openapi/freelo-api.yaml';
$outputDir = __DIR__ . '/../src/Generated/Model';

if (!file_exists($specPath)) {
    fwrite(STDERR, "OpenAPI spec not found at {$specPath}\n");
    fwrite(STDERR, "Download it first: curl -o .openapi/freelo-api.yaml https://api.freelo.io/docs/v1/freelo-api.yaml\n");
    exit(1);
}

$spec = Yaml::parseFile($specPath);
$schemas = $spec['components']['schemas'] ?? [];

if (empty($schemas)) {
    fwrite(STDERR, "No schemas found in OpenAPI spec\n");
    exit(1);
}

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Clean existing generated files
$existingFiles = glob($outputDir . '/*.php');
if ($existingFiles !== false) {
    foreach ($existingFiles as $file) {
        unlink($file);
    }
}

$generated = 0;

foreach ($schemas as $schemaName => $schema) {
    // Skip schemas that are just references or have no properties
    if (isset($schema['$ref'])) {
        continue;
    }

    $resolvedSchema = resolveAllOf($schema, $schemas);
    $properties = $resolvedSchema['properties'] ?? [];

    if (empty($properties)) {
        continue;
    }

    $required = $resolvedSchema['required'] ?? [];
    $className = sanitizeClassName($schemaName);
    $code = generateModelClass($className, $properties, $required, $schemas);

    file_put_contents("{$outputDir}/{$className}.php", $code);
    $generated++;
}

echo "Generated {$generated} model classes in src/Generated/Model/\n";

// --- Helper Functions ---

/**
 * @param array<string, mixed> $schema
 * @param array<string, mixed> $allSchemas
 * @return array<string, mixed>
 */
function resolveAllOf(array $schema, array $allSchemas): array
{
    if (!isset($schema['allOf'])) {
        return $schema;
    }

    $merged = [];
    $mergedProperties = [];
    $mergedRequired = [];

    foreach ($schema['allOf'] as $part) {
        if (isset($part['$ref'])) {
            $refName = resolveRefName($part['$ref']);
            if (isset($allSchemas[$refName])) {
                $resolved = resolveAllOf($allSchemas[$refName], $allSchemas);
                $mergedProperties = array_merge($mergedProperties, $resolved['properties'] ?? []);
                $mergedRequired = array_merge($mergedRequired, $resolved['required'] ?? []);
            }
        } else {
            $mergedProperties = array_merge($mergedProperties, $part['properties'] ?? []);
            $mergedRequired = array_merge($mergedRequired, $part['required'] ?? []);
        }
    }

    $merged['properties'] = $mergedProperties;
    $merged['required'] = array_unique($mergedRequired);

    return $merged;
}

function resolveRefName(string $ref): string
{
    // #/components/schemas/Foo -> Foo
    $parts = explode('/', $ref);
    return end($parts);
}

function sanitizeClassName(string $name): string
{
    // Convert to PascalCase, remove invalid characters
    $name = str_replace(['-', '_', '.'], ' ', $name);
    $name = ucwords($name);
    $name = str_replace(' ', '', $name);
    return $name;
}

function snakeToCamel(string $snake): string
{
    return lcfirst(str_replace('_', '', ucwords($snake, '_')));
}

/**
 * @param array<string, mixed> $properties
 * @param string[] $required
 * @param array<string, mixed> $allSchemas
 */
function generateModelClass(
    string $className,
    array $properties,
    array $required,
    array $allSchemas,
): string {
    $constructorParams = [];
    $fromArrayAssignments = [];

    foreach ($properties as $propName => $propSchema) {
        $camelName = snakeToCamel($propName);
        $isRequired = in_array($propName, $required, true);
        $nullable = !$isRequired || (isset($propSchema['nullable']) && $propSchema['nullable']);
        $phpType = resolvePhpType($propSchema, $allSchemas);
        $typeDecl = $nullable && $phpType !== 'mixed' ? "?{$phpType}" : $phpType;
        $default = $isRequired ? '' : ($nullable && $phpType !== 'mixed' ? ' = null' : '');

        if ($phpType === 'array') {
            $default = ' = []';
            $typeDecl = 'array';
        }

        $constructorParams[] = "        public readonly {$typeDecl} \${$camelName}{$default},";
        $fromArrayAssignments[] = generateFromArrayLine($propName, $camelName, $phpType, $nullable);
    }

    // Add data property for raw API response preservation
    $constructorParams[] = "        /** @var array<string, mixed> */";
    $constructorParams[] = "        public readonly array \$data = [],";

    $constructorBlock = implode("\n", $constructorParams);
    $fromArrayBlock = implode("\n", $fromArrayAssignments);

    return <<<PHP
<?php

/**
 * @generated Auto-generated from OpenAPI spec — do not edit manually.
 * @see scripts/generate-models.php
 */

declare(strict_types=1);

namespace Freelo\\Sdk\\Generated\\Model;

class {$className}
{
    public function __construct(
{$constructorBlock}
    ) {
    }

    /**
     * @param array<string, mixed> \$data
     */
    public static function fromArray(array \$data): self
    {
        return new self(
{$fromArrayBlock}
            data: \$data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return \$this->data;
    }
}

PHP;
}

/**
 * @param array<string, mixed> $propSchema
 * @param array<string, mixed> $allSchemas
 */
function resolvePhpType(array $propSchema, array $allSchemas): string
{
    if (isset($propSchema['$ref'])) {
        return 'mixed';
    }

    if (isset($propSchema['enum'])) {
        return 'string';
    }

    $type = $propSchema['type'] ?? 'mixed';

    return match ($type) {
        'integer' => 'int',
        'number' => 'float',
        'string' => 'string',
        'boolean' => 'bool',
        'array' => 'array',
        'object' => 'array',
        default => 'mixed',
    };
}

function generateFromArrayLine(string $apiName, string $camelName, string $phpType, bool $nullable): string
{
    $cast = match ($phpType) {
        'int' => "(int) ",
        'float' => "(float) ",
        'string' => "(string) ",
        'bool' => "(bool) ",
        default => "",
    };

    if ($phpType === 'array') {
        return "            {$camelName}: isset(\$data['{$apiName}']) && is_array(\$data['{$apiName}'])"
            . "\n                ? \$data['{$apiName}'] : [],";
    }

    if ($nullable) {
        return "            {$camelName}: isset(\$data['{$apiName}'])"
            . " ? {$cast}\$data['{$apiName}'] : null,";
    }

    if ($phpType === 'mixed') {
        return "            {$camelName}: \$data['{$apiName}'] ?? null,";
    }

    $default = match ($phpType) {
        'int' => '0',
        'float' => '0.0',
        'string' => "''",
        'bool' => 'false',
        default => 'null',
    };

    return "            {$camelName}: {$cast}(\$data['{$apiName}'] ?? {$default}),";
}
