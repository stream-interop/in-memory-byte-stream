<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use ReflectionClass;
use RuntimeException;
use StreamInterop\Interface\ClosableStream;

/**
 * A lazy-ghost object for a fully-readable file stream; it opens the
 * encapsulated resource only on first property access.
 */
class ReadableFile extends ReadableFileStream implements ClosableStream
{
    /**
     * @param non-empty-string $filename
     */
    public function __construct(public readonly string $filename)
    {
        $ref = new ReflectionClass($this);
        $ref->resetAsLazyGhost(
            $this,
            $this->openResource(...),
            ReflectionClass::SKIP_INITIALIZATION_ON_SERIALIZE|ReflectionClass::SKIP_DESTRUCTOR
        );
        $ref->getProperty('filename')->skipLazyInitialization($this);
        $ref->getProperty('filename')->setValue($this, $filename);
    }

    /**
     * @inheritdoc
     */
    public function close() : void
    {
        $this->voidOrThrow(fclose($this->resource), "Could not close stream.");
    }

    protected function openResource() : void
    {
        $errorLevel = error_reporting(0);
        error_clear_last();
        $resource = fopen($this->filename, 'rb');
        error_reporting($errorLevel);

        if (! $resource) {
            throw new RuntimeException(
                "Could not open rb resource for {$this->filename}"
            );
        }

        $this->setResource($resource);
    }
}
