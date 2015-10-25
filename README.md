# lnc-azureFileService
File Service for Microsoft Azure SDK for PHP  


# Getting Started
##Install via Composer

1. Create a file named **composer.json** in the root of your project and add the following code to it:

```
{
  "require": {
    "lnc/azure-file-service": "^0"
  },
  "repositories":      [
    {
      "type": "pear",
      "url":  "http://pear.php.net"
    }
  ],
  "minimum-stability": "dev"
}
```

# Usage

## Getting Started

```
<?php
    require_once __DIR__ . '/vendor/autoload.php';
    use WindowsFileService\Common\ServicesBuilder;

    $accountName = '';
    $accountKey  = '';
    $isSecure    = true;

    $connectionString = sprintf(
            'DefaultEndpointsProtocol=%s;AccountName=%s;AccountKey=%s',
            $isSecure
                    ? 'https'
                    : 'http',
            $accountName,
            $accountKey
    );

    $fileRestProxy = ServicesBuilder::getInstance()
                                    ->createFileService( $connectionString );


    $result = $fileRestProxy->createShare(
                                'test'
    );
    
    $result = $fileRestProxy->createDirectory(
                                'test',
                                'test'
    );
        
    $result = $fileRestProxy->createFile(
                            'test',
                            'test',
                            'test.txt',
                            4
    );
    
    $result = $fileRestProxy->createFileContents(
                                'test',
                                'test',
                                'test.txt',
                                'test'
    );
    
    OR

    $result = $fileRestProxy->createFileRange(
                            'test',
                            'test',
                            'test.txt',
                            new \WindowsFileService\File\Models\FileRange( 0, 3 ),
                            'test'
    );
    
```
