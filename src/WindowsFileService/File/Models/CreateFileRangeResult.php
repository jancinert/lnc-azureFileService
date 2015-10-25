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
    use WindowsAzure\Common\Internal\Validate;
    use WindowsFileService\Common\Internal\Resources;

    /**
     * Holds result of calling create or clear file ranges
     *
     * @author    Jan Cinert
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/jancinert/lnc-azureFileService
     */
    class CreateFileRangeResult {
        /**
         * @var \DateTime
         */
        private $_lastModified;

        /**
         * @var string
         */
        private $_etag;

        /**
         * @var string
         */
        private $_contentMD5;

        /**
         * Creates CreateFileRangeResult object from $parsed response in array
         * representation
         *
         * @param array $headers HTTP response headers
         *
         * @return CreateFileRangeResult
         */
        public static function create( $headers ) {
            $result = new CreateFileRangeResult();
            $clean  = array_change_key_case( $headers );

            $date = $clean[Resources::LAST_MODIFIED];
            $date = Utilities::rfc1123ToDateTime( $date );
            $result->setETag( $clean[Resources::ETAG] );
            $result->setLastModified( $date );
            $result->setContentMD5(
                   Utilities::tryGetValue(
                            $clean,
                            Resources::CONTENT_MD5
                   )
            );

            return $result;
        }

        /**
         * Gets file lastModified.
         *
         * @return \DateTime.
         */
        public function getLastModified() {
            return $this->_lastModified;
        }

        /**
         * Sets file lastModified.
         *
         * @param \DateTime $lastModified value.
         *
         * @return none.
         */
        public function setLastModified( $lastModified ) {
            Validate::isDate( $lastModified );
            $this->_lastModified = $lastModified;
        }

        /**
         * Gets file etag.
         *
         * @return string.
         */
        public function getETag() {
            return $this->_etag;
        }

        /**
         * Sets file etag.
         *
         * @param string $etag value.
         *
         * @return none.
         */
        public function setETag( $etag ) {
            Validate::isString(
                    $etag,
                    'etag'
            );
            $this->_etag = $etag;
        }

        /**
         * Gets file contentMD5.
         *
         * @return string.
         */
        public function getContentMD5() {
            return $this->_contentMD5;
        }

        /**
         * Sets file contentMD5.
         *
         * @param string $contentMD5 value.
         *
         * @return none.
         */
        public function setContentMD5( $contentMD5 ) {
            $this->_contentMD5 = $contentMD5;
        }
    }


