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
     * @category  Microsoft
     * @package   WindowsAzure\Common\Internal
     * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
     * @author    Jan Cinert
     * @copyright 2012 Microsoft Corporation
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/windowsazure/azure-sdk-for-php
     * @link      https://github.com/jancinert/lnc-azureFileService
     */

    namespace WindowsFileService\Common\Internal;

    use WindowsAzure\Common\Internal\StorageServiceSettings as BaseStorageServiceSettings;
    use WindowsAzure\Common\Internal\Utilities;
    use WindowsAzure\Common\Internal\Validate;

    /**
     * Represents the settings used to sign and access a request against the storage
     * service. For more information about storage service connection strings check this
     * page: http://msdn.microsoft.com/en-us/library/ee758697
     *
     * @category  Microsoft
     * @package   WindowsAzure\Common\Internal
     * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
     * @author    Jan Cinert
     * @copyright 2012 Microsoft Corporation
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/windowsazure/azure-sdk-for-php
     * @link      https://github.com/jancinert/lnc-azureFileService
     */
    class StorageServiceSettings extends BaseStorageServiceSettings {
        /**
         * The storage service name.
         *
         * @var string
         */
        private $_name;

        /**
         * A base64 representation.
         *
         * @var string
         */
        private $_key;

        /**
         * The endpoint for the blob service.
         *
         * @var string
         */
        private $_blobEndpointUri;

        /**
         * The endpoint for the queue service.
         *
         * @var string
         */
        private $_queueEndpointUri;

        /**
         * The endpoint for the table service.
         *
         * @var string
         */
        private $_tableEndpointUri;

        /**
         * The endpoint for the file service.
         *
         * @var string
         */
        private $_fileEndpointUri;

        /**
         * @var StorageServiceSettings
         */
        private static $_devStoreAccount;

        /**
         * Validator for the UseDevelopmentStorage setting. Must be "true".
         *
         * @var array
         */
        private static $_useDevelopmentStorageSetting;

        /**
         * Validator for the DevelopmentStorageProxyUri setting. Must be a valid Uri.
         *
         * @var array
         */
        private static $_developmentStorageProxyUriSetting;

        /**
         * Validator for the DefaultEndpointsProtocol setting. Must be either "http"
         * or "https".
         *
         * @var array
         */
        private static $_defaultEndpointsProtocolSetting;

        /**
         * Validator for the AccountName setting. No restrictions.
         *
         * @var array
         */
        private static $_accountNameSetting;

        /**
         * Validator for the AccountKey setting. Must be a valid base64 string.
         *
         * @var array
         */
        private static $_accountKeySetting;

        /**
         * Validator for the BlobEndpoint setting. Must be a valid Uri.
         *
         * @var array
         */
        private static $_blobEndpointSetting;

        /**
         * Validator for the QueueEndpoint setting. Must be a valid Uri.
         *
         * @var array
         */
        private static $_queueEndpointSetting;

        /**
         * Validator for the TableEndpoint setting. Must be a valid Uri.
         *
         * @var array
         */
        private static $_tableEndpointSetting;

        /**
         * Validator for the FileEndpoint setting. Must be a valid Uri.
         *
         * @var array
         */
        private static $_fileEndpointSetting;

        /**
         * @var boolean
         */
        protected static $isInitialized = false;

        /**
         * Holds the expected setting keys.
         *
         * @var array
         */
        protected static $validSettingKeys = array();

        /**
         * Initializes static members of the class.
         *
         * @return none
         */
        protected static function init() {
            self::$_useDevelopmentStorageSetting = self::setting(
                                                       Resources::USE_DEVELOPMENT_STORAGE_NAME,
                                                       'true'
            );

            self::$_developmentStorageProxyUriSetting = self::settingWithFunc(
                                                            Resources::DEVELOPMENT_STORAGE_PROXY_URI_NAME,
                                                            Validate::getIsValidUri()
            );

            self::$_defaultEndpointsProtocolSetting = self::setting(
                                                          Resources::DEFAULT_ENDPOINTS_PROTOCOL_NAME,
                                                          'http',
                                                          'https'
            );

            self::$_accountNameSetting = self::setting( Resources::ACCOUNT_NAME_NAME );

            self::$_accountKeySetting = self::settingWithFunc(
                                            Resources::ACCOUNT_KEY_NAME,
                                                    // base64_decode will return false if the $key is not in base64 format.
                                                    function ( $key ) {
                                                        $isValidBase64String = base64_decode(
                                                                $key,
                                                                true
                                                        );
                                                        if( $isValidBase64String ) {
                                                            return true;
                                                        }
                                                        else {
                                                            throw new \RuntimeException( sprintf(
                                                                                                 Resources::INVALID_ACCOUNT_KEY_FORMAT,
                                                                                                 $key
                                                                                         ) );
                                                        }
                                                    }
            );

            self::$_blobEndpointSetting = self::settingWithFunc(
                                              Resources::BLOB_ENDPOINT_NAME,
                                              Validate::getIsValidUri()
            );

            self::$_queueEndpointSetting = self::settingWithFunc(
                                               Resources::QUEUE_ENDPOINT_NAME,
                                               Validate::getIsValidUri()
            );

            self::$_tableEndpointSetting = self::settingWithFunc(
                                               Resources::TABLE_ENDPOINT_NAME,
                                               Validate::getIsValidUri()
            );

            self::$_fileEndpointSetting = self::settingWithFunc(
                                              Resources::FILE_ENDPOINT_NAME,
                                              Validate::getIsValidUri()
            );

            self::$validSettingKeys[] = Resources::USE_DEVELOPMENT_STORAGE_NAME;
            self::$validSettingKeys[] = Resources::DEVELOPMENT_STORAGE_PROXY_URI_NAME;
            self::$validSettingKeys[] = Resources::DEFAULT_ENDPOINTS_PROTOCOL_NAME;
            self::$validSettingKeys[] = Resources::ACCOUNT_NAME_NAME;
            self::$validSettingKeys[] = Resources::ACCOUNT_KEY_NAME;
            self::$validSettingKeys[] = Resources::BLOB_ENDPOINT_NAME;
            self::$validSettingKeys[] = Resources::QUEUE_ENDPOINT_NAME;
            self::$validSettingKeys[] = Resources::TABLE_ENDPOINT_NAME;
            self::$validSettingKeys[] = Resources::FILE_ENDPOINT_NAME;
        }

        /**
         * Creates new storage service settings instance.
         *
         * @param string $name             The storage service name.
         * @param string $key              The storage service key.
         * @param string $blobEndpointUri  The storage service blob endpoint.
         * @param string $queueEndpointUri The storage service queue endpoint.
         * @param string $tableEndpointUri The storage service table endpoint.
         * @param string $fileEndpointUri  The storage service file endpoint.
         */
        public function __construct( $name, $key, $blobEndpointUri, $queueEndpointUri, $tableEndpointUri, $fileEndpointUri ) {
            $this->_name             = $name;
            $this->_key              = $key;
            $this->_blobEndpointUri  = $blobEndpointUri;
            $this->_queueEndpointUri = $queueEndpointUri;
            $this->_tableEndpointUri = $tableEndpointUri;
            $this->_fileEndpointUri  = $fileEndpointUri;
        }

        /**
         * Returns a StorageServiceSettings with development storage credentials using
         * the specified proxy Uri.
         *
         * @param string $proxyUri The proxy endpoint to use.
         *
         * @return StorageServiceSettings
         */
        private static function _getDevelopmentStorageAccount( $proxyUri ) {
            if( is_null( $proxyUri ) ) {
                return self::developmentStorageAccount();
            }

            $scheme = parse_url(
                    $proxyUri,
                    PHP_URL_SCHEME
            );
            $host   = parse_url(
                    $proxyUri,
                    PHP_URL_HOST
            );
            $prefix = $scheme . "://" . $host;

            return new StorageServiceSettings( Resources::DEV_STORE_NAME, Resources::DEV_STORE_KEY, $prefix . ':10000/devstoreaccount1/', $prefix . ':10001/devstoreaccount1/', $prefix . ':10002/devstoreaccount1/', null );
        }

        /**
         * Gets a StorageServiceSettings object that references the development storage
         * account.
         *
         * @return StorageServiceSettings
         */
        public static function developmentStorageAccount() {
            if( is_null( self::$_devStoreAccount ) ) {
                self::$_devStoreAccount = self::_getDevelopmentStorageAccount(
                                              Resources::DEV_STORE_URI
                );
            }

            return self::$_devStoreAccount;
        }

        /**
         * Gets the default service endpoint using the specified protocol and account
         * name.
         *
         * @param array  $settings The service settings.
         * @param string $dns      The service DNS.
         *
         * @return string
         */
        private static function _getDefaultServiceEndpoint( $settings, $dns ) {
            $scheme      = Utilities::tryGetValueInsensitive(
                                    Resources::DEFAULT_ENDPOINTS_PROTOCOL_NAME,
                                    $settings
            );
            $accountName = Utilities::tryGetValueInsensitive(
                                    Resources::ACCOUNT_NAME_NAME,
                                    $settings
            );

            return sprintf(
                    Resources::SERVICE_URI_FORMAT,
                    $scheme,
                    $accountName,
                    $dns
            );
        }

        /**
         * Creates StorageServiceSettings object given endpoints uri.
         *
         * @param array  $settings         The service settings.
         * @param string $blobEndpointUri  The blob endpoint uri.
         * @param string $queueEndpointUri The queue endpoint uri.
         * @param string $tableEndpointUri The table endpoint uri.
         *
         * @return \WindowsAzure\Common\Internal\StorageServiceSettings
         */
        private static function _createStorageServiceSettings( $settings, $blobEndpointUri = null, $queueEndpointUri = null, $tableEndpointUri = null, $fileEndpointUri = null ) {
            $blobEndpointUri  = Utilities::tryGetValueInsensitive(
                                         Resources::BLOB_ENDPOINT_NAME,
                                         $settings,
                                         $blobEndpointUri
            );
            $queueEndpointUri = Utilities::tryGetValueInsensitive(
                                         Resources::QUEUE_ENDPOINT_NAME,
                                         $settings,
                                         $queueEndpointUri
            );
            $tableEndpointUri = Utilities::tryGetValueInsensitive(
                                         Resources::TABLE_ENDPOINT_NAME,
                                         $settings,
                                         $tableEndpointUri
            );
            $fileEndpointUri  = Utilities::tryGetValueInsensitive(
                                         Resources::FILE_ENDPOINT_NAME,
                                         $settings,
                                         $fileEndpointUri
            );
            $accountName      = Utilities::tryGetValueInsensitive(
                                         Resources::ACCOUNT_NAME_NAME,
                                         $settings
            );
            $accountKey       = Utilities::tryGetValueInsensitive(
                                         Resources::ACCOUNT_KEY_NAME,
                                         $settings
            );

            return new StorageServiceSettings( $accountName, $accountKey, $blobEndpointUri, $queueEndpointUri, $tableEndpointUri, $fileEndpointUri );
        }

        /**
         * Creates a StorageServiceSettings object from the given connection string.
         *
         * @param string $connectionString The storage settings connection string.
         *
         * @return StorageServiceSettings
         */
        public static function createFromConnectionString( $connectionString ) {
            $tokenizedSettings = self::parseAndValidateKeys( $connectionString );

            // Devstore case
            $matchedSpecs = self::matchedSpecification(
                                $tokenizedSettings,
                                self::allRequired( self::$_useDevelopmentStorageSetting ),
                                self::optional( self::$_developmentStorageProxyUriSetting )
            );
            if( $matchedSpecs ) {
                $proxyUri = Utilities::tryGetValueInsensitive(
                                     Resources::DEVELOPMENT_STORAGE_PROXY_URI_NAME,
                                     $tokenizedSettings
                );

                return self::_getDevelopmentStorageAccount( $proxyUri );
            }

            // Automatic case
            $matchedSpecs = self::matchedSpecification(
                                $tokenizedSettings,
                                self::allRequired(
                                    self::$_defaultEndpointsProtocolSetting,
                                    self::$_accountNameSetting,
                                    self::$_accountKeySetting
                                ),
                                self::optional(
                                    self::$_blobEndpointSetting,
                                    self::$_queueEndpointSetting,
                                    self::$_tableEndpointSetting,
                                    self::$_fileEndpointSetting
                                )
            );
            if( $matchedSpecs ) {
                return self::_createStorageServiceSettings(
                           $tokenizedSettings,
                           self::_getDefaultServiceEndpoint(
                               $tokenizedSettings,
                               Resources::BLOB_BASE_DNS_NAME
                           ),
                           self::_getDefaultServiceEndpoint(
                               $tokenizedSettings,
                               Resources::QUEUE_BASE_DNS_NAME
                           ),
                           self::_getDefaultServiceEndpoint(
                               $tokenizedSettings,
                               Resources::TABLE_BASE_DNS_NAME
                           ),
                           self::_getDefaultServiceEndpoint(
                               $tokenizedSettings,
                               Resources::FILE_BASE_DNS_NAME
                           )
                );
            }

            // Explicit case
            $matchedSpecs = self::matchedSpecification(
                                $tokenizedSettings,
                                self::atLeastOne(
                                    self::$_blobEndpointSetting,
                                    self::$_queueEndpointSetting,
                                    self::$_tableEndpointSetting,
                                    self::$_fileEndpointSetting
                                ),
                                self::allRequired(
                                    self::$_accountNameSetting,
                                    self::$_accountKeySetting
                                )
            );
            if( $matchedSpecs ) {
                return self::_createStorageServiceSettings( $tokenizedSettings );
            }

            self::noMatch( $connectionString );
        }

        /**
         * Gets storage service name.
         *
         * @return string
         */
        public function getName() {
            return $this->_name;
        }

        /**
         * Gets storage service key.
         *
         * @return string
         */
        public function getKey() {
            return $this->_key;
        }

        /**
         * Gets storage service blob endpoint uri.
         *
         * @return string
         */
        public function getBlobEndpointUri() {
            return $this->_blobEndpointUri;
        }

        /**
         * Gets storage service queue endpoint uri.
         *
         * @return string
         */
        public function getQueueEndpointUri() {
            return $this->_queueEndpointUri;
        }

        /**
         * Gets storage service table endpoint uri.
         *
         * @return string
         */
        public function getTableEndpointUri() {
            return $this->_tableEndpointUri;
        }

        /**
         * Gets storage service file endpoint uri.
         *
         * @return string
         */
        public function getFileEndpointUri() {
            return $this->_fileEndpointUri;
        }
    }


