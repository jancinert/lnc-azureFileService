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

    /**
     * optional parameters for createXXXFile wrapper
     *
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/jancinert/lnc-azureFileService
     */
    class CreateFileOptions extends FileServiceOptions {
        /**
         * @var string
         */
        private $_contentType;

        /**
         * @var string
         */
        private $_contentEncoding;

        /**
         * @var string
         */
        private $_contentLanguage;

        /**
         * @var string
         */
        private $_contentMD5;

        /**
         * @var string
         */
        private $_cacheControl;

        /**
         * @var array
         */
        private $_metadata;

        /**
         * @var string
         */
        private $_contentDisposition;

        /**
         * @var AccessCondition
         */
        private $_accessCondition;

        /**
         * Gets file contentType.
         *
         * @return string.
         */
        public function getContentType() {
            return $this->_contentType;
        }

        /**
         * Sets file contentType.
         *
         * @param string $contentType value.
         *
         * @return none.
         */
        public function setContentType( $contentType ) {
            $this->_contentType = $contentType;
        }

        /**
         * Gets contentEncoding.
         *
         * @return string.
         */
        public function getContentEncoding() {
            return $this->_contentEncoding;
        }

        /**
         * Sets contentEncoding.
         *
         * @param string $contentEncoding value.
         *
         * @return none.
         */
        public function setContentEncoding( $contentEncoding ) {
            $this->_contentEncoding = $contentEncoding;
        }

        /**
         * Gets contentLanguage.
         *
         * @return string.
         */
        public function getContentLanguage() {
            return $this->_contentLanguage;
        }

        /**
         * Sets contentLanguage.
         *
         * @param string $contentLanguage value.
         *
         * @return none.
         */
        public function setContentLanguage( $contentLanguage ) {
            $this->_contentLanguage = $contentLanguage;
        }

        /**
         * Gets contentMD5.
         *
         * @return string.
         */
        public function getContentMD5() {
            return $this->_contentMD5;
        }

        /**
         * Sets contentMD5.
         *
         * @param string $contentMD5 value.
         *
         * @return none.
         */
        public function setContentMD5( $contentMD5 ) {
            $this->_contentMD5 = $contentMD5;
        }

        /**
         * Gets cacheControl.
         *
         * @return string.
         */
        public function getCacheControl() {
            return $this->_cacheControl;
        }

        /**
         * Sets cacheControl.
         *
         * @param string $cacheControl value to use.
         *
         * @return none.
         */
        public function setCacheControl( $cacheControl ) {
            $this->_cacheControl = $cacheControl;
        }

        /**
         * Gets access condition
         *
         * @return AccessCondition
         */
        public function getAccessCondition() {
            return $this->_accessCondition;
        }

        /**
         * Sets access condition
         *
         * @param AccessCondition $accessCondition value to use.
         *
         * @return none.
         */
        public function setAccessCondition( $accessCondition ) {
            $this->_accessCondition = $accessCondition;
        }

        /**
         * Gets file metadata.
         *
         * @return array.
         */
        public function getMetadata() {
            return $this->_metadata;
        }

        /**
         * Sets file metadata.
         *
         * @param string $metadata value.
         *
         * @return none.
         */
        public function setMetadata( $metadata ) {
            $this->_metadata = $metadata;
        }

        /**
         * Gets file contentDisposition.
         *
         * @return string.
         */
        public function getContentDisposition() {
            return $this->_contentDisposition;
        }

        /**
         * Sets file contentDisposition.
         *
         * @param string $contentDisposition value.
         *
         * @return none.
         */
        public function setContentDisposition( $contentDisposition ) {
            $this->_contentDisposition = $contentDisposition;
        }
    }


