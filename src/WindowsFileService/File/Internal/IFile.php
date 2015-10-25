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

    namespace WindowsFileService\File\Internal;

    use WindowsAzure\Common\Internal\FilterableService;
    use WindowsAzure\Common\Models\GetServicePropertiesResult;
    use WindowsAzure\Common\Models\ServiceProperties;
    use WindowsFileService\File\Models\CreateFileOptions;
    use WindowsFileService\File\Models\CreateFileRangeOptions;
    use WindowsFileService\File\Models\CreateFileRangeResult;
    use WindowsFileService\File\Models\FileRange;
    use WindowsFileService\File\Models\FileResult;
    use WindowsFileService\File\Models\FileServiceOptions;

    /**
     * This interface has some REST APIs provided by Windows Azure for File service.
     *
     * @author    Jan Cinert
     * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
     * @copyright 2012 Microsoft Corporation
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/jancinert/lnc-azureFileService
     * @see       https://msdn.microsoft.com/en-us/library/azure/dn167006.aspx
     */
    interface IFile extends FilterableService {
        /**
         * Gets the properties of the File service.
         *
         * @param FileServiceOptions $options optional file service options.
         *
         * @return GetServicePropertiesResult
         *
         * @see https://msdn.microsoft.com/en-us/library/azure/mt427369.aspx
         */
        public function getServiceProperties( $options = null );

        /**
         * Sets the properties of the File service.
         *
         * @param ServiceProperties  $serviceProperties new service properties
         * @param FileServiceOptions $options           optional parameters
         *
         * @return none.
         *
         * @see https://msdn.microsoft.com/en-us/library/azure/mt427374.aspx
         */
        public function setServiceProperties( $serviceProperties, $options = null );

        public function createShare( $share, $options = null );

        public function createDirectory( $share, $directoryPath, $options = null );

        /**
         * Creates a new file. Note that calling createFile to create a file
         * only initializes the file.
         * To add content to a file, call createFileContent method.
         *
         * @param string            $share                The share name.
         * @param string            $directoryPath        The directory path.
         * @param string            $file                 The name of file
         * @param int               $length               specifies the maximum size for the
         *                                                file, up to 1 TB. The file size must be aligned to a 512-byte
         *                                                boundary.
         * @param CreateFileOptions $options              optional parameters
         *
         * @return FileResult
         *
         * @see https://msdn.microsoft.com/en-us/library/azure/dn194271.aspx
         */
        public function createFile( $share, $directoryPath, $file, $length, $options = null );


        /**
         * Creates or updates the content of an existing file.
         *
         * Note that the default content type is application/octet-stream.
         *
         * @param string            $share         The name of the share.
         * @param string            $directoryPath The path of the directory.
         * @param string            $file          The name of the file.
         * @param string|resource   $content       The content of the file.
         * @param CreateFileOptions $options       The optional parameters.
         *
         * @return CreateFileRangeResult|null
         *
         */
        public function createFileContents( $share, $directoryPath, $file, $content, $options = null );

        /**
         * Clears a range from the file.
         *
         * @param string                 $share                name of the share
         * @param string                 $directoryPath        path to the directory
         * @param string                 $file                 name of the file
         * @param FileRange              $range                Can be up to the value of the
         *                                                     file's full size. Note that ranges must be aligned to 512 (0-511, 512-1023)
         * @param CreateFileRangeOptions $options              optional parameters
         *
         * @return CreateFileRangeResult.
         *
         */
        public function clearFileRange( $share, $directoryPath, $file, $range, $options = null );

        /**
         * Creates a range to a file.
         *
         * @param string                 $share                name of the share
         * @param string                 $directoryPath        path to the directory
         * @param string                 $file                 name of the file
         * @param FileRange              $range                Can be up to 4 MB in size
         *                                                     Note that ranges must be aligned to 512 (0-511, 512-1023)
         * @param string                 $content              the range contents.
         * @param CreateFileRangeOptions $options              optional parameters
         *
         * @return CreateFileRangeResult.
         *
         */
        public function createFileRange( $share, $directoryPath, $file, $range, $content, $options = null );

    }


