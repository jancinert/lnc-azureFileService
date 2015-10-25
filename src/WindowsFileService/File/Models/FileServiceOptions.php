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
     * File service options.
     *
     * @author    Jan Cinert
     * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
     * @link      https://github.com/jancinert/lnc-azureFileService
     */
    class FileServiceOptions {
        private $_timeout;

        /**
         * Gets timeout.
         *
         * @return string.
         */
        public function getTimeout() {
            return $this->_timeout;
        }

        /**
         * Sets timeout.
         *
         * @param string $timeout value.
         *
         * @return none.
         */
        public function setTimeout( $timeout ) {
            $this->_timeout = $timeout;
        }
    }


