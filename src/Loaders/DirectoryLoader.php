<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Loaders;

use Twirelab\LaravelRouter\Interfaces\Loader as LoaderInterface;
use Twirelab\LaravelRouter\Traits\Loader;

class DirectoryLoader implements LoaderInterface
{
    use Loader;

    /**
     * Load routes.
     */
    public function load(mixed $source): void
    {
        foreach ($source as $so) {
            foreach (glob($so) as $path) {
                $this->loadController($this->getClassFromPath($path));
            }
        }
    }

    /**
     * Get a class from the path.
     */
    private function getClassFromPath(string $path): string
    {
        return $this->getClassNamespaceFromPath($path).'\\'.$this->getClassNameFromPath($path);
    }

    /**
     * Get a class namespace from the path.
     */
    private function getClassNamespaceFromPath(string $path): ?string
    {
        $tokens = token_get_all(file_get_contents($path));
        $count = count($tokens);
        $i = 0;
        $namespace = null;

        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }

        return $namespace;
    }

    /**
     * Get a class name from the path.
     */
    private function getClassNameFromPath(string $path): string
    {
        $classes = [];
        $tokens = token_get_all(file_get_contents($path));

        for ($i = 2; $i < count($tokens); $i++) {
            if ($tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING
            ) {

                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }

        return $classes[0];
    }
}
