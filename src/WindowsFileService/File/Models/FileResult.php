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
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/jancinert/lnc-azureFileService
     */
    namespace WindowsFileService\File\Models;

    use WindowsAzure\Common\Internal\Utilities;
    use WindowsFileService\Common\Internal\Resources;

    /**
     * The result of calling file API.
     *
     * @author    Jan Cinert
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/jancinert/lnc-azureFileService
     * */
    class FileResult {
        /**
         * @var string
         */
        private $_etag;

        /**
         * @var \DateTime
         */
        private $_lastModified;

        /**
         * Creates FileResult object from the response of the file request.
         *
         * @param array $headers The HTTP response headers in array representation.
         *
         * @return FileResult
         */
        public static function create( $headers ) {
            $result = new FileResult();
            $result->setETag(
                   Utilities::tryGetValueInsensitive(
                            Resources::ETAG,
                            $headers
                   )
            );
            if( Utilities::arrayKeyExistsInsensitive(
                         Resources::LAST_MODIFIED,
                         $headers
            )
            ) {
                $lastModified = Utilities::tryGetValueInsensitive(
                                         Resources::LAST_MODIFIED,
                                         $headers
                );
                $result->setLastModified( Utilities::rfc1123ToDateTime( $lastModified ) );
            }

            return $result;
        }

        /**
         * Gets ETag.
         *
         * @return string
         */
        public function getETag() {
            return $this->_etag;
        }

        /**
         * Sets ETag.
         *
         * @param string $etag value.
         *
         * @return none
         */
        public function setETag( $etag ) {
            $this->_etag = $etag;
        }

        /**
         * Gets file lastModified.
         *
         * @return \DateTime
         */
        public function getLastModified() {
            return $this->_lastModified;
        }

        /**
         * Sets file lastModified.
         *
         * @param \DateTime $lastModified value.
         *
         * @return none
         */
        public function setLastModified( $lastModified ) {
            $this->_lastModified = $lastModified;
        }
    }


