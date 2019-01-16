# Download Service

Static instantiation of a new download:

```php
$response = Download::create($stream, $response, 'awesome.jpg');

$response = (new Download())
    ->setFileStream($stream)
    ->setFilename('awesome.jpg')
    ->applyToResponse($response);
```

The following example will create a new response object and use it for the download:

```php
$response = (new Download($streamFactory, $responseFactory))
    ->setFileStream($stream)
    ->setFilename('awesome.jpg')
    ->getResponse();

// Same as above but not required to set the file name in this case
$response = (new Download($streamFactory, $responseFactory))
    ->setFileFromString('path/to/awesome.jpg')
    ->getResponse();
```

Using the factory:

```php
$factory = (new DownloadFactory($streamFactory, $responseFactory))
    ->createDownload()
    ->setFileStream($stream)
    ->setFilename('awesome.jpg')
    ->getResponse();
```
