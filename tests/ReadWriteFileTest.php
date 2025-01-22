<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use RuntimeException;

class ReadWriteFileTest extends TestCase
{
    public function newReadWriteFile() : ReadWriteFile
    {
        return new ReadWriteFile($this->fakeFile());
    }

    public function testCannotOpen() : void
    {
        $stream = new ReadWriteFile('noSuchWrapper://foobar');
        $this->expectException(RuntimeException::CLASS);
        $this->expectExceptionMessage('Could not open rb+ resource for noSuchWrapper://foobar');
        $this->assertFalse(is_resource($stream->resource));
    }

    public function testClose() : void
    {
        $stream = $this->newReadWriteFile();
        $stream->close();
        $this->assertTrue($stream->isClosed());
    }
}
