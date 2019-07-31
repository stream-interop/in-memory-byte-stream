# InMemoryByteStream

> An implementation of the [stream-interop](https://github.com/stream-interop/stream-interop) interfaces for dealing with byte-only in-memory streams.

## Installation

The recommended way of installing this package is via [Composer]():

```cli
$ composer require stream-interop/in-memory-byte-stream
```

## Usage

```php
$byteStream = new Interop\Stream\ByteStream\InMemory('hello');

$byteStream->read(2);   // returns 'he'
$byteStream->getContents();  // returns 'llo'
$byteStream->write(' world');

$byteStream->rewind();
$byteStream->getContents(); // returns 'hello world'
```

## Contributing

See the [CONTRIBUTING.md](CONTRIBUTING.md) file for more information.

## Credits/Authors

## License

The MIT License.

See the [LICENSE.md](LICENSE.md) file for more information.
