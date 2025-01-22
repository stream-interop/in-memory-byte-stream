<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function fakeFile() : string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'fake-file.txt';
    }

    protected function fopenFakeFile(string $mode)
    {
        return fopen($this->fakeFile(), $mode);
    }
}
