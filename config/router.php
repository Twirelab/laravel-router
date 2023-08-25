<?php

use Twirelab\LaravelRouter\Enums\Versioning;

return [
    /**
     * What versions of endpoints are acceptable.
     */
    'acceptable_versions' => [env('ACCEPTABLE_VERSIONS', Versioning::NEUTRAL->value)],
];