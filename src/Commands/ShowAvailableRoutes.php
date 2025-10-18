<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Twirelab\LaravelRouter\Enums\Version;

class ShowAvailableRoutes extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'laravel-router:list {--version= : Filter by specific version}';

    /**
     * The console command description.
     */
    protected $description = 'Show all available routes registered by Laravel Router';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $routes = $this->getLaravelRouterRoutes();

        if ($routes->isEmpty()) {
            $this->info('No routes found registered by Laravel Router.');
            return self::SUCCESS;
        }

        $versionFilter = $this->option('version');
        if ($versionFilter !== null) {
            $routes = $routes->filter(function (Route $route) use ($versionFilter) {
                $version = $route->getAction('laravel_router_version');
                return $this->matchesVersion($version, $versionFilter);
            });
        }

        $this->displayRoutes($routes);

        return self::SUCCESS;
    }

    /**
     * Get routes registered by Laravel Router.
     */
    private function getLaravelRouterRoutes()
    {
        return collect(RouteFacade::getRoutes())
            ->filter(function (Route $route) {
                return $route->getAction('laravel_router_version') !== null;
            });
    }

    /**
     * Display routes in table format.
     */
    private function displayRoutes($routes): void
    {
        $headers = ['URL', 'Method', 'Name', 'Controller', 'Version'];
        $rows = [];

        foreach ($routes as $route) {
            $version = $route->getAction('laravel_router_version');
            $versionString = $this->formatVersion($version);

            $rows[] = [
                $route->uri(),
                implode('|', $route->methods()),
                $route->getName() ?? '-',
                $this->getControllerName($route),
                $versionString,
            ];
        }

        $this->table($headers, $rows);
    }

    /**
     * Get controller name from route.
     */
    private function getControllerName(Route $route): string
    {
        $action = $route->getAction();

        if (isset($action['controller'])) {
            return $action['controller'];
        }

        if (isset($action['uses'])) {
            return $action['uses'];
        }

        return '-';
    }

    /**
     * Format version for display.
     */
    private function formatVersion(int|Version $version): string
    {
        if ($version === Version::NEUTRAL) {
            return 'NEUTRAL';
        }

        return (string) $version;
    }

    /**
     * Check if version matches filter.
     */
    private function matchesVersion(int|Version $version, string $filter): bool
    {
        if ($filter === 'neutral' && $version === Version::NEUTRAL) {
            return true;
        }

        if (is_numeric($filter) && $version === (int) $filter) {
            return true;
        }

        return false;
    }
}
