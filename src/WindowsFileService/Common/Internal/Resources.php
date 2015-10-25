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

    namespace WindowsFileService\Common\Internal;

    use WindowsAzure\Common\Internal\Resources as BaseResources;

    class Resources extends BaseResources {

        const FILE_ENDPOINT_NAME = 'FileEndpoint';
        const FILE_BASE_DNS_NAME = 'file.core.windows.net';
        const FILE_TYPE          = 'File';

        // HTTP Headers
        const FILE_API_LATEST_VERSION  = '2015-04-05';
        const X_MS_CONTENT_MD5         = 'x-ms-content-md5';
        const X_MS_CONTENT_DISPOSITION = 'x-ms-content-disposition';
        const X_MS_TYPE                = 'x-ms-type';
        const X_MS_WRITE               = 'x-ms-write';
        const X_MS_CONTENT_LENGTH      = 'x-ms-content-length';
        const X_MS_SHARE_QUOTA         = 'x-ms-share-quota';

        // Type
        const FILE_TYPE_NAME = 'IFile';

    }