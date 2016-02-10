<?php

    namespace WindowsFileService;

    use WindowsAzure\Common\ServiceException;
    use WindowsFileService\Common\ServicesBuilder;
    use WindowsFileService\File\Models;

    class Facade {

        /**
         * @var string
         */
        protected $connectionString;

        /**
         * @param $connectionString
         */
        public function __construct( $connectionString ) {
            $this->connectionString = $connectionString;
        }

        /**
         * Creates or updates the content of an existing file.
         *
         * Note that the default content type is application/octet-stream.
         *
         * @param string                   $share         The name of the share.
         * @param string                   $directoryPath The path of the directory.
         * @param string                   $fileName      The name of the file.
         * @param string|                  $filePath      The path of the file.
         * @param Models\CreateFileOptions $options       The optional parameters.
         *
         *
         */
        public function uploadFile( $share, $directoryPath, $fileName, $filePath, $options = null ) {

            $fileRestProxy = $this->getFileRestProxy();

            $fileSize = filesize( $filePath );
            $resource = fopen(
                    $filePath,
                    'r'
            );

            try {
                $fileRestProxy->createShare(
                              $share,
                              $options
                );
            }
            catch( ServiceException $e ) {
                if( !( $e->getCode() == 409 ) ) {
                    throw $e;
                }
            }

            try {
                $fileRestProxy->createDirectory(
                              $share,
                              $directoryPath,
                              $options
                );
            }
            catch( ServiceException $e ) {
                if( !( $e->getCode() == 409 ) ) {
                    throw $e;
                }
            }

            $fileRestProxy->createFile(
                          $share,
                          $directoryPath,
                          $fileName,
                          $fileSize,
                          $options
            );

            $fileRestProxy->createFileContents(
                          $share,
                          $directoryPath,
                          $fileName,
                          $resource,
                          $options
            );
        }

        protected function getFileRestProxy() {
            $fileRestProxy = ServicesBuilder::getInstance()
                                            ->createFileService( $this->connectionString );

            return $fileRestProxy;
        }

    }