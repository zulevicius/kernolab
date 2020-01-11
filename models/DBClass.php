<?php

    class DBClass
    {
        private $host = 'localhost';
        private $username = 'root';
        private $password = '';
        private $database = 'kernotransactions';

        private static $instance = null;
        private $conn;

        private function __construct()
        {
            $this->conn = null;
            try {
                $this->conn = new \PDO(
                    'mysql:host=' . $this->host . ';dbname=' . $this->database,
                    $this->username,
                    $this->password
                );
            } catch (\PDOException $exception) {
                echo 'Error: ' . $exception->getMessage();
            }
        }

        public static function getInstance()
        {
            if (!self::$instance) {
                self::$instance = new DBClass();
            }
            return self::$instance;
        }

        public function getConnection()
        {
            return $this->conn;
        }
    }