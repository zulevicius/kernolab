<?php

    abstract class API
    {
        const HTTP_OK = 200;
        const HTTP_NOT_FOUND = 404;
        const HTTP_ERROR = 500;

        protected $reqMethod;
        protected $endpoint;
        protected $data;

        public function __construct()
        {
            header('Access-Control-Allow-Orgin: *');
            header('Access-Control-Allow-Methods: *');
            header('Content-Type: application/json');

            if (isset($_SERVER['REQUEST_METHOD'])) {
                $this->reqMethod = strtolower($_SERVER['REQUEST_METHOD']);
            }
            if (isset($_GET['endpoint'])) {
                $this->endpoint = strtolower($_GET['endpoint']);
            }
            $this->data = json_decode(file_get_contents('php://input'), true);
            $this->processAPI();
        }

        public function processAPI()
        {
            $method = $this->reqMethod . '_' . $this->endpoint;
            if (method_exists($this, $method)) {
                $this->response($this->$method($this->data));
            } else {
                $errMsg = 'No Endpoint: ' . strtoupper($this->reqMethod) . ' ' . $this->endpoint;
                $this->response(new Response(404, ['error' => $errMsg]));
            }
        }

        private function response(Response $resp)
        {
            header('HTTP/1.1 ' . $resp->getHttpStatusCode() . ' ' . $this->requestStatus($resp->getHttpStatusCode()));
            echo $resp->getJsonData();
        }

        private function requestStatus(int $code): string
        {
            $status = [
                self::HTTP_OK => 'OK',
                self::HTTP_NOT_FOUND => 'Not Found',
                self::HTTP_ERROR => 'Internal Server Error',
            ];
            return $status[$code] ? $status[$code] : $status[self::HTTP_ERROR];
        }
    }