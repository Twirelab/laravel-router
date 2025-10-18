<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Annotations\Router;
use Twirelab\LaravelRouter\Enums\Version;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class ShowUnavailableRoutes extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'laravel-router:unavailable {--path= : Path to scan for controllers}';

    /**
     * The console command description.
     */
    protected $description = 'Show routes that are unavailable due to version filtering';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->option('path') ?? app_path('Http/Controllers');
        $path = is_string($path) ? $path : (is_array($path) ? implode(',', $path) : (string) $path);

        if (!is_dir($path)) {
            $this->error("Directory {$path} does not exist.");
            return self::FAILURE;
        }

        $unavailableRoutes = $this->scanForUnavailableRoutes($path);

        if ($unavailableRoutes->isEmpty()) {
            $this->info('No unavailable routes found.');
            return self::SUCCESS;
        }

        $this->displayUnavailableRoutes($unavailableRoutes);

        return self::SUCCESS;
    }

    /**
     * Scan directory for unavailable routes.
     */
    private function scanForUnavailableRoutes(string $path): \Illuminate\Support\Collection
    {
        $unavailableRoutes = collect();
        $activeVersions = config('laravel-router.active_versions', []);

        $this->scanDirectory($path, $unavailableRoutes, $activeVersions);

        return $unavailableRoutes;
    }

    /**
     * Recursively scan directory for controllers.
     */
    private function scanDirectory(string $path, \Illuminate\Support\Collection $unavailableRoutes, array $activeVersions): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $this->scanController($file->getPathname(), $unavailableRoutes, $activeVersions);
            }
        }
    }

    /**
     * Scan individual controller file.
     */
    private function scanController(string $filePath, \Illuminate\Support\Collection $unavailableRoutes, array $activeVersions): void
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return;
        }

        // Basic check if file contains Laravel Router annotations
        if (!str_contains($content, 'Twirelab\\LaravelRouter\\Annotations\\')) {
            return;
        }

        try {
            $className = $this->extractClassName($content);
            if (!$className || !class_exists($className)) {
                return;
            }

            $reflection = new ReflectionClass($className);
            if ($reflection->isAbstract()) {
                return;
            }

            $this->analyzeController($reflection, $unavailableRoutes, $activeVersions);
        } catch (\Exception $e) {
            // Skip files that can't be analyzed
        }
    }

    /**
     * Extract class name from file content.
     */
    private function extractClassName(string $content): ?string
    {
        if (
            preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches) &&
            preg_match('/class\s+(\w+)/', $content, $classMatches)
        ) {
            return $namespaceMatches[1] . '\\' . $classMatches[1];
        }

        return null;
    }

    /**
     * Analyze controller for unavailable routes.
     */
    private function analyzeController(ReflectionClass $class, \Illuminate\Support\Collection $unavailableRoutes, array $activeVersions): void
    {
        // Get controller version
        $controllerVersion = $this->getControllerVersion($class);

        foreach ($class->getMethods() as $method) {
            if ($method->isPublic() && !$method->isConstructor()) {
                $this->analyzeMethod($class, $method, $controllerVersion, $unavailableRoutes, $activeVersions);
            }
        }
    }

    /**
     * Get controller version from Router annotation.
     */
    private function getControllerVersion(ReflectionClass $class): int|Version|null
    {
        $attributes = $class->getAttributes(Router::class, ReflectionAttribute::IS_INSTANCEOF);

        if (!empty($attributes)) {
            $annotation = $attributes[0]->newInstance();
            return $annotation->getVersion();
        }

        return null;
    }

    /**
     * Analyze method for unavailable routes.
     */
    private function analyzeMethod(ReflectionClass $class, ReflectionMethod $method, int|Version|null $controllerVersion, \Illuminate\Support\Collection $unavailableRoutes, array $activeVersions): void
    {
        $methodAttributes = $method->getAttributes(Method::class, ReflectionAttribute::IS_INSTANCEOF);

        foreach ($methodAttributes as $attribute) {
            $annotation = $attribute->newInstance();
            $version = $annotation->getVersion() ?? $controllerVersion;

            if (!$this->isVersionActive($version, $activeVersions)) {
                $unavailableRoutes->push([
                    'url' => $annotation->getUri(),
                    'method' => $annotation->getMethod(),
                    'name' => $annotation->getName() ?? Str::snake($method->getName()),
                    'controller' => $class->getName() . '@' . $method->getName(),
                    'version' => $this->formatVersion($version),
                    'reason' => $this->getUnavailableReason($version, $activeVersions),
                ]);
            }
        }
    }

    /**
     * Check if version is active.
     */
    private function isVersionActive(int|Version|null $version, array $activeVersions): bool
    {
        $normalizedVersion = $this->normalizeVersion($version);

        if ($normalizedVersion === Version::NEUTRAL) {
            return true;
        }

        return in_array($normalizedVersion, $activeVersions, true);
    }

    /**
     * Normalize version to integer or NEUTRAL.
     */
    private function normalizeVersion(int|Version|null $version): int|Version
    {
        if ($version === null) {
            return Version::NEUTRAL;
        }

        if ($version instanceof Version) {
            return $version;
        }

        return $version;
    }

    /**
     * Get reason why route is unavailable.
     */
    private function getUnavailableReason(int|Version|null $version, array $activeVersions): string
    {
        $normalizedVersion = $this->normalizeVersion($version);

        if ($normalizedVersion === Version::NEUTRAL) {
            return 'Always available';
        }

        $versionString = $normalizedVersion instanceof Version ? $normalizedVersion->name : (string) $normalizedVersion;
        return "Version {$versionString} not in active versions: " . implode(', ', $activeVersions);
    }

    /**
     * Format version for display.
     */
    private function formatVersion(int|Version|null $version): string
    {
        if ($version === null) {
            return 'NEUTRAL';
        }

        if ($version instanceof Version) {
            return $version->name;
        }

        return (string) $version;
    }

    /**
     * Display unavailable routes in table format.
     */
    private function displayUnavailableRoutes(\Illuminate\Support\Collection $routes): void
    {
        $headers = ['URL', 'Method', 'Name', 'Controller', 'Version', 'Reason'];
        $rows = [];

        foreach ($routes as $route) {
            $rows[] = [
                $route['url'],
                $route['method'],
                $route['name'],
                $route['controller'],
                $route['version'],
                $route['reason'],
            ];
        }

        $this->table($headers, $rows);
    }
}
