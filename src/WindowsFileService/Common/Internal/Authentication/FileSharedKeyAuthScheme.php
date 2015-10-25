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

    namespace WindowsFileService\Common\Internal\Authentication;

    use WindowsAzure\Common\Internal\Authentication\SharedKeyAuthScheme;
    use WindowsAzure\Common\Internal\Utilities;
    use WindowsFileService\Common\Internal\Resources;

    /**
     * Provides shared key authentication scheme for file. For more info
     * check: http://msdn.microsoft.com/en-us/library/windowsazure/dd179428.aspx
     *
     * @author    Jan Cinert
     * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
     * @copyright 2012 Microsoft Corporation
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/jancinert/lnc-azureFileService
     */
    class FileSharedKeyAuthScheme extends SharedKeyAuthScheme {

        /**
         * Computes the authorization signature for file shared key.
         *
         * @param array  $headers     request headers.
         * @param string $url         reuqest url.
         * @param array  $queryParams query variables.
         * @param string $httpMethod  request http method.
         *
         * @see File Services (Shared Key Authentication) at
         *      http://msdn.microsoft.com/en-us/library/windowsazure/dd179428.aspx
         *
         * @return string
         */
        protected function computeSignature( $headers, $url, $queryParams, $httpMethod ) {
            $canonicalizedHeaders = parent::computeCanonicalizedHeaders( $headers );

            $canonicalizedResource = parent::computeCanonicalizedResource(
                                           $url,
                                           $queryParams
            );


            $stringToSign   = array();
            $stringToSign[] = strtoupper( $httpMethod );

            foreach( $this->includedHeaders as $header ) {
                $v = Utilities::tryGetValue(
                              $headers,
                              $header
                );
                if( $header == Resources::CONTENT_LENGTH && $v == 0 ) {
                    $v = '';
                }
                $stringToSign[] = $v;
            }

            if( count( $canonicalizedHeaders ) > 0 ) {
                $stringToSign[] = implode(
                        "\n",
                        $canonicalizedHeaders
                );
            }

            $stringToSign[] = $canonicalizedResource;
            $stringToSign   = implode(
                    "\n",
                    $stringToSign
            );

            return $stringToSign;
        }

    }


