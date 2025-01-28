# stream-interop/impl

Reference implementations of [stream-interop/interface][].

## Installation

Install this package via [Composer][]:

```
$ composer require stream-interop/impl
```

## Implementations

### File Streams

These streams operate on an already-opened resource.

#### _ConsumableFileStream_

A unidirectional readable stream; does not afford seeking or writing.

```php
$stream = new \StreamInterop\Impl\ConsumableFileStream(fopen('/path/to/file', 'rb'));

$stream->read(2);       // reads the first 2 bytes
$stream->read(4);       // reads the next 4 bytes
$stream->getContents(); // reads all remaining bytes
$stream->eof();         // true
```

#### _ReadableFileStream_

A readable stream that affords seeking and stringability.

```php
$stream = new \StreamInterop\Impl\ReadableFileStream(fopen('/path/to/file', 'rb'));

$stream->read(2);       // reads the first 2 bytes
$stream->read(4);       // reads the next 4 bytes
$stream->getContents(); // reads all remaining bytes
$stream->eof();         // true
$stream->__toString();  // rewinds and reads the entire file

$stream->rewind();      // rewinds the pointer
$stream->seek(7);       // moves to byte 7
$stream->read(3);       // reads the next 3 bytes
```

#### _ReadWriteFileStream_

A fully read+write stream that affords seeking and stringability.

```php
$stream = new \StreamInterop\Impl\ReadWriteFileStream(fopen('/path/to/file', 'wb+'));

$stream->write('Hello World!');
$stream->rewind();
$stream->read(2);           // reads "He"
$stream->read(4);           // reads "llo "
$stream->getContents();     // reads "World!"
$stream->eof();             // true
$stream->__toString();      // reads "Hello World!"
```

#### _WritableFileStream_

A unidirectional writable stream; does not afford seeking or reading.

```php
$stream = new \StreamInterop\Impl\WritableFileStream(fopen('/path/to/file', 'wb'));

$stream->write('Hello World!');
```

### Lazy Ghost File Objects

These streams open a resource themselves on a specified filename.

#### _ReadableFile_

A readable lazy-opening stream that affords seeking and stringability.

```php
// construct with a file name, not an open resource
$stream = new \StreamInterop\Impl\ReadableFile('/path/to/file');

$stream->read(2);           // reads the first 2 bytes
$stream->read(4);           // reads the next 4 bytes
$stream->getContents();     // reads all remaining bytes
$stream->eof();             // true
$stream->__toString();      // rewinds and reads the entire file

$stream->rewind();          // rewinds the pointer
$stream->seek(7);           // moves to byte 7
$stream->read(3);           // reads the next 3 bytes
```
#### _ReadWriteFile_

A fully read+write lazy-opening stream that affords seeking and stringability.

```php
// construct with a file name, not an open resource
$stream = new \StreamInterop\Impl\ReadWriteFile('/path/to/file');

$stream->write('Hello World!');
$stream->rewind();
$stream->read(2);           // reads "He"
$stream->read(4);           // reads "llo "
$stream->getContents();     // reads "World!"
$stream->eof();             // true
$stream->__toString();      // reads "Hello World!"
```

### Non-Resource Streams

#### _StringStream_

A fully read+write stream that affords seeking and stringability, backed by a string instead of a resource.

```php
// construct with string data, not a resource of file name
$stream = new \StreamInterop\Impl\StringStream('Hello');

$stream->read(2);           // reads 'He'
$stream->getContents();     // reads 'llo'
$stream->write(' World!');

$stream->rewind();
$stream->getContents();     // reads 'Hello World!'
```

* * *

[stream-interop/interface]: https://packagist.org/packages/stream-interop/interface
[Composer]: https://getcomposer.org
