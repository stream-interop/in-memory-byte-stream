<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @return non-empty-string
     */
    protected function fakeFile() : string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'fake-file.txt';
    }

    /**
     * @return resource
     */
    protected function fopenFakeFile(string $mode) : mixed
    {
        $resource = fopen($this->fakeFile(), $mode);
        assert(is_resource($resource));
        return $resource;
    }
}
