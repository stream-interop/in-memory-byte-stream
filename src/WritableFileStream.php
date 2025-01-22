<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use StreamInterop\Interface\WritableStream;
use Stringable;

class WritableFileStream extends FileStream implements WritableStream
{
    protected function setResource(mixed $resource) : void
    {
        parent::setResource($resource);
        $this->assertWritable();
    }

    /**
     * @inheritdoc
     */
    public function write(string|Stringable $data) : int
    {
        return $this->intOrThrow(
            fwrite($this->resource, (string) $data),
            "Could not write to stream.",
        );
    }
}
