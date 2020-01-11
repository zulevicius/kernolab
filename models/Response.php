<?php

    class Response
    {
        private $httpStatusCode;
        private $jsonData;

        /**
         * Response constructor.
         * @param int $httpStatusCode
         * @param mixed $data argument will be JSON-encoded
         */
        public function __construct(int $httpStatusCode, $data)
        {
            $this->httpStatusCode = $httpStatusCode;
            $this->jsonData = json_encode($data);
        }

        /**
         * @return int
         */
        public function getHttpStatusCode(): int
        {
            return $this->httpStatusCode;
        }

        /**
         * @return false|string
         */
        public function getJsonData()
        {
            return $this->jsonData;
        }
    }