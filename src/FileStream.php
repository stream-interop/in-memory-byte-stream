<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use LogicException;
use RuntimeException;
use StreamInterop\Interface\ResourceStream;
use StreamInterop\Interface\SizableStream;

/**
 * Basic functionality for file resources; state-reporting only (no reading,
 * seeking, or writing) with support methods for extended classes.
 */
class FileStream implements ResourceStream, SizableStream
{
    /**
     * @inheritdoc
     */
    public array $metadata {
        get {
            return stream_get_meta_data($this->resource);
        }
    }

    /**
     * @inheritdoc
     */
    public protected(set) mixed $resource;

    /**
     * @param resource $resource
     */
    public function __construct(mixed $resource)
    {
        $this->setResource($resource);
    }

    /**
     * @inheritdoc
     */
    public function getSize() : ?int
    {
        /**
         * @see https://www.php.net/manual/en/function.stat.php
         *
         * @var array{
         *     dev:int<0,max>,
         *     ino:int<0,max>,
         *     mode:int<0,max>,
         *     nlink:int<0,max>,
         *     uid:int<0,max>,
         *     gid:int<0,max>,
         *     rdev:int<0,max>,
         *     size:int<0,max>,
         *     atime:int<0,max>,
         *     mtime:int<0,max>,
         *     ctime:int<0,max>,
         *     blksize:int<0,max>,
         *     blocks:int<0,max>,
         * } $stat
         */
        $stat = fstat($this->resource);
        return $stat['size'];
    }

    /**
     * @inheritdoc
     */
    public function isClosed() : bool
    {
        return strtolower(get_resource_type($this->resource)) === 'unknown';
    }

    /**
     * @inheritdoc
     */
    public function isOpen() : bool
    {
        return strtolower(get_resource_type($this->resource)) === 'stream';
    }

    protected function setResource(mixed $resource) : void
    {
        if (
            is_resource($resource)
            && get_resource_type($resource) === 'stream'
        ) {
            $this->resource = $resource;
            return;
        }

        $type = get_debug_type($resource);

        throw new LogicException(
            "Expected resource (stream), got {$type}."
        );
    }

    protected function intOrThrow(bool|int $result, string $message, false|int $failure = false) : int
    {
        if ($result === $failure) {
            throw new RuntimeException($message);
        }

        /** @var int */
        return $result;
    }

    protected function stringOrThrow(false|string $result, string $message) : string
    {
        if ($result === false) {
            throw new RuntimeException($message);
        }

        /** @var string */
        return $result;
    }

    protected function voidOrThrow(mixed $result, string $message, false|int $failure = false) : void
    {
        if ($result === $failure) {
            throw new RuntimeException($message);
        }
    }

    protected function assertReadable() : void
    {
        $mode = $this->metadata['mode'];

        $readable = strstr($mode, 'r') !== false
            || strstr($mode, '+') !== false;

        if (! $readable) {
            throw new LogicException("Resource is not readable.");
        }
    }

    protected function assertSeekable() : void
    {
        $seekable = $this->metadata['seekable'];

        if (! $seekable) {
            throw new LogicException("Resource is not seekable.");
        }
    }

    protected function assertWritable() : void
    {
        $mode = $this->metadata['mode'];

        $writable = strstr($mode, 'w') !== false
            || strstr($mode, '+') !== false;

        if (! $writable) {
            throw new LogicException("Resource is not writable.");
        }
    }
}
