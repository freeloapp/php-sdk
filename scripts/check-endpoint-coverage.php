<?php

/**
 * Endpoint Coverage / Drift Check
 *
 * Compares the operations declared in the OpenAPI spec against the endpoints
 * actually called by the hand-written resource classes in src/Resource/.
 *
 * Unlike `composer generate`, the resource layer is NOT regenerated from the
 * spec — so a spec change that adds or renames an endpoint (without touching a
 * model schema) produces no diff and no update PR. This check closes that gap:
 * it fails when the spec exposes an operation that no resource implements, so
 * the weekly update-api-spec workflow surfaces endpoint drift instead of
 * silently shipping a client that lags upstream.
 *
 * Endpoints intentionally left unimplemented can be listed in
 * .openapi/endpoint-coverage-ignore.txt (one normalized "METHOD /path" per
 * line, with `{}` for path params; `#` comments and blank lines allowed).
 *
 * Usage:
 *   php scripts/check-endpoint-coverage.php
 *   composer check:endpoints
 *
 * Exit codes:
 *   0 — every spec operation is implemented (or ignored)
 *   1 — spec operations missing from the SDK (drift)
 *   2 — setup error (spec/resources not found)
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$specPath = __DIR__ . '/../.openapi/freelo-api.yaml';
$resourceDir = __DIR__ . '/../src/Resource';
$ignorePath = __DIR__ . '/../.openapi/endpoint-coverage-ignore.txt';

if (!file_exists($specPath)) {
    fwrite(STDERR, "OpenAPI spec not found at {$specPath}\n");
    fwrite(STDERR, "Run `make spec` or download it manually first.\n");
    exit(2);
}
if (!is_dir($resourceDir)) {
    fwrite(STDERR, "Resource directory not found at {$resourceDir}\n");
    exit(2);
}

$httpMethods = ['get', 'post', 'put', 'patch', 'delete'];

// --- 1. Operations declared in the spec ---

$spec = Yaml::parseFile($specPath);
$paths = $spec['paths'] ?? [];
if (empty($paths) || !is_array($paths)) {
    fwrite(STDERR, "No paths found in OpenAPI spec\n");
    exit(2);
}

/** @var array<string, string> $specOps  normalized key => human label */
$specOps = [];
foreach ($paths as $path => $pathItem) {
    if (!is_array($pathItem)) {
        continue;
    }
    foreach ($httpMethods as $method) {
        if (!isset($pathItem[$method]) || !is_array($pathItem[$method])) {
            continue;
        }
        $specOps[normalizeKey($method, $path)] = strtoupper($method) . ' ' . $path;
    }
}

// --- 2. Endpoints implemented by the resource classes ---

/** @var array<string, string> $implOps  normalized key => source label */
$implOps = [];
/** @var list<string> $dynamic  call sites whose path could not be resolved */
$dynamic = [];

foreach (glob($resourceDir . '/*Resource.php') ?: [] as $file) {
    $src = file_get_contents($file);
    if ($src === false) {
        continue;
    }
    $shortName = basename($file);

    // Resolve the literals returned by getEndpoint()/getSingleEndpoint(), which
    // several resources pass straight into client->get()/post().
    $endpoint = matchReturnLiteral($src, 'getEndpoint');
    $single = matchReturnLiteral($src, 'getSingleEndpoint');

    // Match client->METHOD( <first-arg> across newlines. First arg is either a
    // string literal or a $this->getEndpoint()/getSingleEndpoint() call.
    // uploadFile() is a multipart POST helper on the client.
    $pattern = '/client->(get|post|put|patch|delete|uploadFile)\s*\(\s*'
        . '(?:"([^"]*)"|\'([^\']*)\'|\$this->(getEndpoint|getSingleEndpoint)\(\))/s';
    preg_match_all($pattern, $src, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $method = $m[1] === 'uploadFile' ? 'post' : $m[1];
        if (!empty($m[4])) {
            $raw = $m[4] === 'getEndpoint' ? $endpoint : $single;
        } else {
            // Double-quoted content is group 2; single-quoted is group 3.
            $raw = ($m[2] ?? '') !== '' ? $m[2] : ($m[3] ?? '');
        }

        if ($raw === null || $raw === '') {
            $dynamic[] = "{$shortName}: " . strtoupper($method) . ' (unresolved path)';
            continue;
        }

        $implOps[normalizeKey($method, '/' . ltrim($raw, '/'))] = $shortName;
    }
}

// --- 3. Ignore list ---

/** @var array<string, bool> $ignored */
$ignored = [];
if (file_exists($ignorePath)) {
    foreach (file($ignorePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        // Normalize ignore entries the same way: "METHOD /path".
        [$im, $ip] = array_pad(explode(' ', $line, 2), 2, '');
        if ($ip === '') {
            continue;
        }
        $ignored[normalizeKey($im, $ip)] = true;
    }
}

// --- 4. Diff ---

$missing = [];   // in spec, not implemented
foreach ($specOps as $key => $label) {
    if (isset($implOps[$key]) || isset($ignored[$key])) {
        continue;
    }
    $missing[$key] = $label;
}

$extra = [];     // implemented, not in spec (possible stale/renamed endpoint)
foreach ($implOps as $key => $source) {
    if (isset($specOps[$key]) || isset($ignored[$key])) {
        continue;
    }
    $extra[$key] = $source;
}

ksort($missing);
ksort($extra);

// --- 5. Report ---

$specCount = count($specOps);
$implCount = count($implOps);
echo "Endpoint coverage: {$implCount} resource call(s) vs {$specCount} spec operation(s).\n";

if (!empty($dynamic)) {
    echo "\nℹ️  " . count($dynamic) . " call site(s) with dynamically-built paths (not checked):\n";
    foreach ($dynamic as $d) {
        echo "    - {$d}\n";
    }
}

if (!empty($extra)) {
    echo "\n⚠️  " . count($extra) . " resource endpoint(s) not found in the spec "
        . "(renamed/removed upstream, or normalization mismatch):\n";
    foreach ($extra as $key => $source) {
        echo "    - {$key}  ({$source})\n";
    }
}

if (!empty($missing)) {
    echo "\n❌ ENDPOINT DRIFT: " . count($missing) . " spec operation(s) not implemented by any resource:\n";
    foreach ($missing as $label) {
        echo "    - {$label}\n";
    }
    echo "\nImplement these in src/Resource/, or add the normalized key to\n";
    echo "{$ignorePath} if intentionally unsupported.\n";
    exit(1);
}

echo "\n✅ Every spec operation is implemented or ignored. No endpoint drift.\n";
exit(0);

// --- Helpers ---

/**
 * Normalize a (method, path) pair into a comparable key. Path params in either
 * spec form ({task_id}) or PHP-interpolation form ({$taskId}) collapse to {},
 * trailing slashes drop, and any query string is stripped.
 */
function normalizeKey(string $method, string $path): string
{
    $path = explode('?', $path, 2)[0];
    $path = preg_replace('/\{[^}]*\}/', '{}', $path) ?? $path;
    $path = '/' . trim($path, '/');
    return strtoupper($method) . ' ' . $path;
}

/**
 * Extract the string literal returned by a zero-arg method, e.g.
 * `protected function getEndpoint(): string { return 'tasks'; }` => "tasks".
 */
function matchReturnLiteral(string $src, string $methodName): ?string
{
    $pattern = '/function\s+' . preg_quote($methodName, '/')
        . '\s*\([^)]*\)\s*:\s*string\s*\{\s*return\s*(?:"([^"]*)"|\'([^\']*)\')/s';
    if (preg_match($pattern, $src, $m)) {
        return ($m[1] ?? '') !== '' ? $m[1] : ($m[2] ?? null);
    }
    return null;
}
