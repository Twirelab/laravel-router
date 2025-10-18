# Router Versioning Implementation Summary

This document summarizes the implementation of the versioning functionality for Laravel Router.

## Features Implemented

### 1. Version Enum (`src/Enums/Version.php`)

- `NEUTRAL` (value: 0) - Routes that are always available regardless of active versions
- `V1`, `V2`, `V3` - Specific version cases
- Helper methods: `isNeutral()`, `getValue()`, `fromInt()`

### 2. Configuration File (`config/laravel-router.php`)

Three configuration options:

- `active_versions`: Array of currently active API versions (e.g., `[1, 2]`)
- `version_in_url`: Boolean to enable/disable version prefix in URLs (default: `true`)
- `version_prefix`: Custom prefix for version in URLs (default: `'v'`)
  - `'v'` → `/v1/auth/login`
  - `''` → `/1/auth/login`
  - `'api'` → `/api1/auth/login`

### 3. Updated Annotations

Both `Router` and `Method` annotations now support a `version` parameter:

```php
#[Router(version: 1)] // Controller-level version
class AuthController {
    #[Method('/login', Methods::POST, version: 2)] // Method-level version
    public function login() { ... }
}
```

### 4. Version Filtering Logic

- Routes with versions not in `active_versions` are not registered
- Routes with `Version::NEUTRAL` or no version are always registered
- Method version overrides controller version
- Version prefix is automatically added to URIs when enabled

### 5. Artisan Commands

#### ShowAvailableRoutes (`laravel-router:list`)

Displays all currently registered routes from Laravel Router:

- Columns: URL, Method, Name, Controller, Version
- Option: `--version=X` to filter by specific version
- Table format for easy reading

#### ShowUnavailableRoutes (`laravel-router:unavailable`)

Scans controllers and shows routes that are filtered out:

- Columns: URL, Method, Name, Controller, Version, Reason
- Option: `--path=/custom/path` to scan specific directory
- Shows why routes are unavailable (version not active)

## Usage Examples

### Basic Versioning

```php
// Controller with version 1
#[Router(version: 1)]
class UserController {
    #[Method('/users', Methods::GET)]
    public function index() { ... } // Will be /v1/users
}
```

### Method Override

```php
#[Router(version: 1)]
class UserController {
    #[Method('/users', Methods::GET, version: 2)]
    public function index() { ... } // Will be /v2/users (method overrides controller)
}
```

### Neutral Version

```php
class HealthController {
    #[Method('/health', Methods::GET, version: Version::NEUTRAL)]
    public function check() { ... } // Will be /health (no version prefix)
}
```

### Configuration

```php
// config/laravel-router.php
return [
    'active_versions' => [1, 2, 3],  // Only v1, v2, v3 routes will be registered
    'version_in_url' => true,        // Enable version in URL
    'version_prefix' => 'v',         // Use 'v' prefix (/v1/, /v2/, etc.)
];
```

## Testing

All versioning functionality is fully tested:

- Unit tests for Version enum
- Unit tests for updated annotations
- Feature tests for version filtering
- Feature tests for version inheritance
- Feature tests for URL prefixing
- Feature tests for configuration options

## Publishing Configuration

Users can publish the configuration file:

```bash
php artisan vendor:publish --tag=laravel-router-config
```

## Commands Usage

```bash
# Show all available routes
php artisan laravel-router:list

# Show routes for specific version
php artisan laravel-router:list --version=1

# Show unavailable routes
php artisan laravel-router:unavailable

# Show unavailable routes in custom path
php artisan laravel-router:unavailable --path=app/Http/Controllers
```

## Notes

- Routes without version are treated as `Version::NEUTRAL` (always available)
- Version metadata is stored in route actions for command display
- Version inheritance: controller version → method version (method wins)
- URL building respects `version_in_url` and `version_prefix` settings
