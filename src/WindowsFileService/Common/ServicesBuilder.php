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

    namespace WindowsFileService\Common;

    use WindowsAzure\Common\Internal\Filters\AuthenticationFilter;
    use WindowsAzure\Common\Internal\Filters\DateFilter;
    use WindowsAzure\Common\Internal\Filters\HeadersFilter;
    use WindowsAzure\Common\Internal\Utilities;
    use WindowsAzure\Common\ServicesBuilder as BaseServicesBuilder;
    use WindowsFileService\Common\Internal\Authentication\FileSharedKeyAuthScheme;
    use WindowsFileService\Common\Internal\Resources;
    use WindowsFileService\Common\Internal\StorageServiceSettings;
    use WindowsFileService\File\FileRestProxy;

    class ServicesBuilder extends BaseServicesBuilder {
        /**
         * @var ServicesBuilder
         */
        private static $_instance = null;

        /**
         * Gets the File authentication scheme.
         *
         * @param string $accountName The account name.
         * @param string $accountKey  The account key.
         *
         * @return \WindowsAzure\Common\Internal\Authentication\StorageAuthScheme
         */
        protected function fileAuthenticationScheme( $accountName, $accountKey ) {
            return new FileSharedKeyAuthScheme( $accountName, $accountKey );
        }

        /**
         * Builds a file object.
         *
         * @param string $connectionString The configuration connection string.
         *
         * @return \WindowsFileService\File\Internal\IFile
         */
        public function createFileService( $connectionString ) {
            $settings = StorageServiceSettings::createFromConnectionString(
                                              $connectionString
            );

            $httpClient = $this->httpClient();
            $serializer = $this->serializer();
            $uri        = Utilities::tryAddUrlScheme(
                                   $settings->getFileEndpointUri()
            );

            $fileWrapper = new FileRestProxy( $httpClient, $uri, $settings->getName(), $serializer );

            // Adding headers filter
            $headers = array(
                    Resources::USER_AGENT => Resources::SDK_USER_AGENT,
            );

            $headers[Resources::X_MS_VERSION] = Resources::FILE_API_LATEST_VERSION;

            $headersFilter = new HeadersFilter( $headers );
            $fileWrapper   = $fileWrapper->withFilter( $headersFilter );

            // Adding date filter
            $dateFilter  = new DateFilter();
            $fileWrapper = $fileWrapper->withFilter( $dateFilter );

            $authFilter = new AuthenticationFilter( $this->fileAuthenticationScheme(
                                                         $settings->getName(),
                                                         $settings->getKey()
                                                    ) );

            $fileWrapper = $fileWrapper->withFilter( $authFilter );

            return $fileWrapper;
        }

        /**
         * Gets the static instance of this class.
         *
         * @return ServicesBuilder
         */
        public static function getInstance() {
            if( !isset( self::$_instance ) ) {
                self::$_instance = new ServicesBuilder();
            }

            return self::$_instance;
        }
    }