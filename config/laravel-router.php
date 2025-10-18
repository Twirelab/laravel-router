<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Active Versions
    |--------------------------------------------------------------------------
    |
    | List of currently active API versions. Routes with versions not in this
    | array will be skipped during registration (unless they use NEUTRAL version).
    |
    */
    'active_versions' => [1, 2],

    /*
    |--------------------------------------------------------------------------
    | Version in URL
    |--------------------------------------------------------------------------
    |
    | Whether to include version prefix in the route URLs.
    | When enabled, routes will be prefixed with version (e.g., /v1/auth/login).
    |
    */
    'version_in_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Version Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix used for version in URL. Examples:
    | 'v' => /v1/auth/login
    | '' => /1/auth/login
    | 'api' => /api1/auth/login
    |
    */
    'version_prefix' => 'v',
];
