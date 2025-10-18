<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Loaders;

use Twirelab\LaravelRouter\Exceptions\InvalidControllerException;
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
            $paths = glob($so);
            if ($paths === false) {
                throw InvalidControllerException::invalidGlobPattern($so);
            }

            foreach ($paths as $path) {
                $className = $this->getClassFromPath($path);
                $this->loadController($className);
            }
        }
    }

    /**
     * Get a class from the path.
     */
    private function getClassFromPath(string $path): string
    {
        $fileContent = file_get_contents($path);
        if ($fileContent === false) {
            throw InvalidControllerException::fileReadError($path);
        }

        $tokens = token_get_all($fileContent);

        $namespace = $this->extractNamespaceFromTokens($tokens);
        $className = $this->extractClassNameFromTokens($tokens);

        if ($className === null) {
            throw InvalidControllerException::noClassInFile($path);
        }

        return $namespace ? $namespace . '\\' . $className : $className;
    }

    /**
     * Extract namespace from tokens.
     */
    private function extractNamespaceFromTokens(array $tokens): ?string
    {
        $count = count($tokens);
        $i = 0;
        $namespace = null;

        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace = trim($namespace ?? '');
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
     * Extract class name from tokens.
     */
    private function extractClassNameFromTokens(array $tokens): ?string
    {
        for ($i = 2; $i < count($tokens); $i++) {
            if (
                is_array($tokens[$i - 2]) && $tokens[$i - 2][0] === T_CLASS
                && is_array($tokens[$i - 1]) && $tokens[$i - 1][0] === T_WHITESPACE
                && is_array($tokens[$i]) && $tokens[$i][0] === T_STRING
            ) {
                return $tokens[$i][1];
            }
        }

        return null;
    }
}
