<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use StreamInterop\Interface\ReadableStream;
use StreamInterop\Interface\SeekableStream;
use StreamInterop\Interface\StringableStream;

/**
 * A read-only stream to consume the resource without seeking or writing;
 * good for remote streams.
 */
class ConsumableFileStream extends FileStream implements ReadableStream
{
    protected function setResource(mixed $resource) : void
    {
        parent::setResource($resource);
        $this->assertReadable();
    }

    /**
     * @inheritdoc
     */
    public function eof() : bool
    {
        return feof($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function getContents() : string
    {
        return $this->stringOrThrow(
            stream_get_contents($this->resource),
            "Could not get contents from stream.",
        );
    }

    /**
     * @inheritdoc
     */
    public function read(int $length) : string
    {
        return $this->stringOrThrow(
            fread($this->resource, $length),
            "Could not read from stream.",
        );
    }
}
