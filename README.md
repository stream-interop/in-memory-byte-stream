# stream-interop/impl

Reference implementations of [stream-interop/interface][].

## Installation

The recommended way of installing this package is via [Composer][]:

```
$ composer require stream-interop/impl
```

## Implementations

### File Streams

#### _FileStream_

#### _ConsumableFileStream_

#### _ReadableFileStream_

#### _ReadWriteFileStream_

#### _WritableFileStream_

### Lazy Ghost File Objects

#### _ReadableFile_

#### _ReadWriteFile_

### Non-Resource Streams

#### _StringStream_

```php
$stream = new \StreamInterop\Impl\StringStream('hello');

$stream->read(2);   // returns 'he'
$stream->getContents();  // returns 'llo'
$stream->write(' world');

$stream->rewind();
$stream->getContents(); // returns 'hello world'
```
