<?php

    /**
     * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     * http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing, software
     * distributed under the License is distributed on an "AS IS" BASIS,
     * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
     * See the License for the specific language governing permissions and
     * limitations under the License.
     *
     * PHP version 5
     *
     * @author    Jan Cinert
     * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
     * @copyright 2012 Microsoft Corporation
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/jancinert/lnc-azureFileService
     */

    namespace WindowsFileService\File;

    use WindowsAzure\Common\Internal\ServiceRestProxy;
    use WindowsAzure\Common\Internal\Validate;
    use WindowsAzure\Common\Models\GetServicePropertiesResult;
    use WindowsAzure\Common\Models\ServiceProperties;
    use WindowsFileService\Common\Internal\Resources;
    use WindowsFileService\File\Internal\IFile;
    use WindowsFileService\File\Models\CreateFileOptions;
    use WindowsFileService\File\Models\CreateFileRangeOptions;
    use WindowsFileService\File\Models\CreateFileRangeResult;
    use WindowsFileService\File\Models\FileRange;
    use WindowsFileService\File\Models\FileResult;
    use WindowsFileService\File\Models\FileServiceOptions;
    use WindowsFileService\File\Models\FileWriteOption;

    /**
     * This class constructs HTTP requests and receive HTTP responses for file
     * service layer.
     *
     * @author    Jan Cinert
     * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
     * @copyright 2012 Microsoft Corporation
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/jancinert/lnc-azureFileService
     */
    class FileRestProxy extends ServiceRestProxy implements IFile {

        /**
         * Creates URI path for file.
         *
         * @param string $share         The share name.
         * @param string $directoryPath The directoryPath name.
         * @param string $file          The file name.
         *
         * @return string
         */
        private function _createPath( $share, $directoryPath = null, $file = null ) {

            $parts = array();

            foreach(
                    array(
                            $share,
                            $directoryPath,
                            $file
                    ) as $part
            ) {

                if( $part === null ) {
                    continue;
                }

                $part = urlencode( $part );
                // Unencode the forward slashes to match what the server expects.
                $part = str_replace(
                        '%2F',
                        '/',
                        $part
                );
                // Unencode the backward slashes to match what the server expects.
                $part = str_replace(
                        '%5C',
                        '/',
                        $part
                );
                // Re-encode the spaces (encoded as space) to the % encoding.
                $part = str_replace(
                        '+',
                        '%20',
                        $part
                );

                $parts[] = $part;
            }

            return implode(
                    '/',
                    $parts
            );
        }

        /**
         * Adds optional create file headers.
         *
         * @param CreateFileOptions $options The optional parameters.
         * @param array             $headers The HTTP request headers.
         *
         * @return array
         */
        private function _addCreateFileOptionalHeaders( $options, $headers ) {
            $contentType = $options->getContentType();
            $metadata    = $options->getMetadata();

            if( !is_null( $contentType ) ) {
                $this->addOptionalHeader(
                     $headers,
                     Resources::CONTENT_TYPE,
                     $options->getContentType()
                );
            }
            else {
                $this->addOptionalHeader(
                     $headers,
                     Resources::CONTENT_TYPE,
                     Resources::BINARY_FILE_TYPE
                );
            }
            $headers = $this->addMetadataHeaders(
                            $headers,
                            $metadata
            );
            $headers = $this->addOptionalAccessConditionHeader(
                            $headers,
                            $options->getAccessCondition()
            );

            $this->addOptionalHeader(
                 $headers,
                 Resources::CONTENT_ENCODING,
                 $options->getContentEncoding()
            );
            $this->addOptionalHeader(
                 $headers,
                 Resources::CONTENT_LANGUAGE,
                 $options->getContentLanguage()
            );
            $this->addOptionalHeader(
                 $headers,
                 Resources::X_MS_CONTENT_MD5,
                 $options->getContentMD5()
            );
            $this->addOptionalHeader(
                 $headers,
                 Resources::CACHE_CONTROL,
                 $options->getCacheControl()
            );
            $this->addOptionalHeader(
                 $headers,
                 Resources::X_MS_CONTENT_DISPOSITION,
                 $options->getContentDisposition()
            );

            return $headers;
        }

        /**
         * Adds Range header to the headers array.
         *
         * @param array   $headers The HTTP request headers.
         * @param integer $start   The start byte.
         * @param integer $end     The end byte.
         *
         * @return array
         */
        private function _addOptionalRangeHeader( $headers, $start, $end ) {
            if( !is_null( $start ) || !is_null( $end ) ) {
                $range      = $start . '-' . $end;
                $rangeValue = 'bytes=' . $range;
                $this->addOptionalHeader(
                     $headers,
                     Resources::RANGE,
                     $rangeValue
                );
            }

            return $headers;
        }

        /**
         * Does actual work for create and clear file ranges.
         *
         * @param string                 $action        Either clear or create.
         * @param string                 $share         The share name.
         * @param string                 $directoryPath The directory path.
         * @param string                 $file          The file name.
         * @param FileRange              $range         The file range.
         * @param string|resource        $content       The content stream.
         * @param CreateFileRangeOptions $options       The optional parameters.
         *
         * @return CreateFileRangeResult
         */
        private function _updateFileRangeImpl( $action, $share, $directoryPath, $file, $range, $content, $options = null ) {
            Validate::isString(
                    $share,
                    'share'
            );
            Validate::notNullOrEmpty(
                    $share,
                    'share'
            );
            Validate::isString(
                    $directoryPath,
                    'directoryPath'
            );
            Validate::isString(
                    $file,
                    'file'
            );
            Validate::isTrue(
                    $range instanceof FileRange,
                    sprintf(
                            Resources::INVALID_PARAM_MSG,
                            'range',
                            get_class( new FileRange() )
                    )
            );
            Validate::isTrue(
                    is_string( $content ) || is_resource( $content ),
                    sprintf(
                            Resources::INVALID_PARAM_MSG,
                            'content',
                            'string|resource'
                    )
            );

            $method      = Resources::HTTP_PUT;
            $headers     = array();
            $queryParams = array();
            $postParams  = array();
            $path        = $this->_createPath(
                                $share,
                                $directoryPath,
                                $file
            );
            $statusCode  = Resources::STATUS_CREATED;
            // If read file failed for any reason it will throw an exception.
            $body = is_resource( $content )
                    ? stream_get_contents( $content )
                    : $content;

            if( is_null( $options ) ) {
                $options = new CreateFileRangeOptions();
            }

            $headers[Resources::CONTENT_LENGTH] = $action == FileWriteOption::CLEAR_OPTION
                    ? 0
                    : strlen( $body );

            $headers = $this->_addOptionalRangeHeader(
                            $headers,
                            $range->getStart(),
                            $range->getEnd()
            );

            $headers = $this->addOptionalAccessConditionHeader(
                            $headers,
                            $options->getAccessCondition()
            );

            $this->addOptionalHeader(
                 $headers,
                 Resources::CONTENT_MD5,
                 $options->getContentMD5()
            );
            $this->addOptionalHeader(
                 $headers,
                 Resources::X_MS_WRITE,
                 $action
            );
            $this->addOptionalHeader(
                 $headers,
                 Resources::CONTENT_TYPE,
                 Resources::URL_ENCODED_CONTENT_TYPE
            );
            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_COMP,
                 'range'
            );
            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_TIMEOUT,
                 $options->getTimeout()
            );

            $response = $this->send(
                             $method,
                             $headers,
                             $queryParams,
                             $postParams,
                             $path,
                             $statusCode,
                             $body
            );

            return CreateFileRangeResult::create( $response->getHeader() );
        }

        /**
         * Gets the properties of the File service.
         *
         * @param Models\FileServiceOptions $options The optional parameters.
         *
         * @return GetServicePropertiesResult
         *
         * @see https://msdn.microsoft.com/en-us/library/azure/mt427369.aspx
         */
        public function getServiceProperties( $options = null ) {
            $method      = Resources::HTTP_GET;
            $headers     = array();
            $queryParams = array();
            $postParams  = array();
            $path        = Resources::EMPTY_STRING;
            $statusCode  = Resources::STATUS_OK;

            if( is_null( $options ) ) {
                $options = new FileServiceOptions();
            }

            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_TIMEOUT,
                 $options->getTimeout()
            );
            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_REST_TYPE,
                 'service'
            );
            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_COMP,
                 'properties'
            );

            $response = $this->send(
                             $method,
                             $headers,
                             $queryParams,
                             $postParams,
                             $path,
                             $statusCode
            );
            $parsed   = $this->dataSerializer->unserialize( $response->getBody() );

            return GetServicePropertiesResult::create( $parsed );
        }

        /**
         * Sets the properties of the File service.
         *
         * It's recommended to use getServiceProperties, alter the returned object and
         * then use setServiceProperties with this altered object.
         *
         * @param ServiceProperties         $serviceProperties The service properties.
         * @param Models\FileServiceOptions $options           The optional parameters.
         *
         * @return none
         *
         * @see https://msdn.microsoft.com/en-us/library/azure/mt427374.aspx
         */
        public function setServiceProperties( $serviceProperties, $options = null ) {
            Validate::isTrue(
                    $serviceProperties instanceof ServiceProperties,
                    Resources::INVALID_SVC_PROP_MSG
            );

            $method      = Resources::HTTP_PUT;
            $headers     = array();
            $queryParams = array();
            $postParams  = array();
            $statusCode  = Resources::STATUS_ACCEPTED;
            $path        = Resources::EMPTY_STRING;
            $body        = $serviceProperties->toXml( $this->dataSerializer );

            if( is_null( $options ) ) {
                $options = new FileServiceOptions();
            }

            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_REST_TYPE,
                 'service'
            );
            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_COMP,
                 'properties'
            );
            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_TIMEOUT,
                 $options->getTimeout()
            );
            $this->addOptionalHeader(
                 $headers,
                 Resources::CONTENT_TYPE,
                 Resources::URL_ENCODED_CONTENT_TYPE
            );

            $this->send(
                 $method,
                 $headers,
                 $queryParams,
                 $postParams,
                 $path,
                 $statusCode,
                 $body
            );
        }

        /**
         * Creates a new share.
         *
         * @param string                   $share   The share name.
         * @param Models\CreateFileOptions $options The optional parameters.
         *
         * @return FileResult
         *
         * @see https://msdn.microsoft.com/en-us/library/azure/dn194271.aspx
         */
        public function createShare( $share, $options = null ) {
            Validate::isString(
                    $share,
                    'share'
            );

            $method      = Resources::HTTP_PUT;
            $headers     = array();
            $postParams  = array();
            $queryParams = array();
            $path        = $this->_createPath(
                                $share
            );
            $statusCode  = Resources::STATUS_CREATED;

            if( is_null( $options ) ) {
                $options = new CreateFileOptions();
            }

            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_REST_TYPE,
                 'share'
            );
            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_TIMEOUT,
                 $options->getTimeout()
            );

            $response = $this->send(
                             $method,
                             $headers,
                             $queryParams,
                             $postParams,
                             $path,
                             $statusCode
            );

            return FileResult::create( $response->getHeader() );
        }

        /**
         * Creates a new share.
         *
         * @param string                   $share         The share name.
         * @param string                   $directoryPath The directory path.
         * @param Models\CreateFileOptions $options       The optional parameters.
         *
         * @return FileResult
         *
         * @see https://msdn.microsoft.com/en-us/library/azure/dn194271.aspx
         */
        public function createDirectory( $share, $directoryPath, $options = null ) {
            Validate::isString(
                    $share,
                    'share'
            );
            Validate::isString(
                    $directoryPath,
                    'directoryPath'
            );

            $method      = Resources::HTTP_PUT;
            $headers     = array();
            $postParams  = array();
            $queryParams = array();
            $path        = $this->_createPath(
                                $share,
                                $directoryPath
            );
            $statusCode  = Resources::STATUS_CREATED;

            if( is_null( $options ) ) {
                $options = new CreateFileOptions();
            }

            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_REST_TYPE,
                 'directory'
            );
            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_TIMEOUT,
                 $options->getTimeout()
            );

            $response = $this->send(
                             $method,
                             $headers,
                             $queryParams,
                             $postParams,
                             $path,
                             $statusCode
            );

            return FileResult::create( $response->getHeader() );
        }

        /**
         * Creates a new file.
         *
         * @param string                   $share         The share name.
         * @param string                   $directoryPath The directory path.
         * @param string                   $file          The name of file
         * @param integer                  $length        Specifies the maximum size for the
         *                                                file, up to 1 TB. The file size must be aligned to a 512-byte
         *                                                boundary.
         * @param Models\CreateFileOptions $options       The optional parameters.
         *
         * @return FileResult
         *
         * @see https://msdn.microsoft.com/en-us/library/azure/dn194271.aspx
         */
        public function createFile( $share, $directoryPath, $file, $length, $options = null ) {
            Validate::isString(
                    $share,
                    'share'
            );
            Validate::isString(
                    $directoryPath,
                    'directoryPath'
            );
            Validate::isString(
                    $file,
                    'file'
            );
            Validate::isInteger(
                    $length,
                    'length'
            );
            Validate::notNull(
                    $length,
                    'length'
            );

            $method      = Resources::HTTP_PUT;
            $headers     = array();
            $postParams  = array();
            $queryParams = array();
            $path        = $this->_createPath(
                                $share,
                                $directoryPath,
                                $file
            );
            $statusCode  = Resources::STATUS_CREATED;

            if( is_null( $options ) ) {
                $options = new CreateFileOptions();
            }

            $headers[Resources::X_MS_TYPE]           = Resources::FILE_TYPE;
            $headers[Resources::X_MS_CONTENT_LENGTH] = $length;
            $headers                                 = $this->_addCreateFileOptionalHeaders(
                                                            $options,
                                                            $headers
            );

            $this->addOptionalQueryParam(
                 $queryParams,
                 Resources::QP_TIMEOUT,
                 $options->getTimeout()
            );

            $response = $this->send(
                             $method,
                             $headers,
                             $queryParams,
                             $postParams,
                             $path,
                             $statusCode
            );

            return FileResult::create( $response->getHeader() );
        }

        /**
         * Creates or updates the content of an existing file.
         *
         * Note that the default content type is application/octet-stream.
         *
         * @param string                   $share         The name of the share.
         * @param string                   $directoryPath The path of the directory.
         * @param string                   $file          The name of the file.
         * @param string|resource          $content       The content of the file.
         * @param Models\CreateFileOptions $options       The optional parameters.
         *
         * @return Models\CreateFileRangeResult|null
         *
         */
        public function createFileContents( $share, $directoryPath, $file, $content, $options = null ) {
            Validate::isString(
                    $share,
                    'share'
            );
            Validate::isString(
                    $directoryPath,
                    'directoryPath'
            );
            Validate::notNullOrEmpty(
                    $directoryPath,
                    'directoryPath'
            );
            Validate::isString(
                    $file,
                    'file'
            );
            Validate::isTrue(
                    is_string( $content ) || is_resource( $content ),
                    sprintf(
                            Resources::INVALID_PARAM_MSG,
                            'content',
                            'string|resource'
                    )
            );

            $response = null;

            if( is_null( $options ) ) {
                $options = new CreateFileOptions();
            }

            // This is for large or failsafe upload
            $end    = 0;
            $offset = 0;
            // if threshold is lower than 4mb, honor threshold, else use 4mb
            $blockSize = 4194304;
            while( !$end ) {
                if( is_resource( $content ) ) {
                    $body = fread(
                            $content,
                            $blockSize
                    );
                    if( feof( $content ) ) {
                        $end = 1;
                    }
                }
                else {
                    if( strlen( $content ) <= $blockSize ) {
                        $body = $content;
                        $end  = 1;
                    }
                    else {
                        $body    = substr(
                                $content,
                                0,
                                $blockSize
                        );
                        $content = substr_replace(
                                $content,
                                '',
                                0,
                                $blockSize
                        );
                    }
                }
                $range = new FileRange( $offset, $offset + strlen( $body ) - 1 );
                $offset += $range->getLength();
                $response = $this->createFileRange(
                                 $share,
                                 $directoryPath,
                                 $file,
                                 $range,
                                 $body,
                                 $options
                );
            }

            return $response;
        }

        /**
         * Clears a range from the file.
         *
         * @param string                        $share         name of the share
         * @param string                        $directoryPath path to the directory
         * @param string                        $file          name of the file
         * @param Models\FileRange              $range         Can be up to the value of the
         *                                                     file's full size. Note that ranges must be aligned to 512 (0-511, 512-1023)
         * @param Models\CreateFileRangeOptions $options       optional parameters
         *
         * @return Models\CreateFileRangeResult.
         *
         */
        public function clearFileRange( $share, $directoryPath, $file, $range, $options = null ) {
            return $this->_updateFileRangeImpl(
                        FileWriteOption::CLEAR_OPTION,
                        $share,
                        $directoryPath,
                        $file,
                        $range,
                        Resources::EMPTY_STRING,
                        $options
            );
        }

        /**
         * Creates a range to a file.
         *
         * @param string                        $share         name of the share
         * @param string                        $directoryPath path to the directory
         * @param string                        $file          name of the file
         * @param Models\FileRange              $range         Can be up to 4 MB in size
         *                                                     Note that ranges must be aligned to 512 (0-511, 512-1023)
         * @param string                        $content       the range contents.
         * @param Models\CreateFileRangeOptions $options       optional parameters
         *
         * @return Models\CreateFileRangeResult.
         *
         */
        public function createFileRange( $share, $directoryPath, $file, $range, $content, $options = null ) {
            return $this->_updateFileRangeImpl(
                        FileWriteOption::UPDATE_OPTION,
                        $share,
                        $directoryPath,
                        $file,
                        $range,
                        $content,
                        $options
            );
        }
    }
