<?php

    abstract class DBEntity
    {
        protected function dbSelect($sql, $bindArr)
        {
            $this->dbQuery($sql, $bindArr, $conn, $stmt);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        protected function dbUpdate($sql, $bindArr)
        {
            $this->dbQuery($sql, $bindArr, $conn, $stmt);
            return $stmt->rowCount();
        }

        protected function dbInsert($sql, $bindArr)
        {
            $this->dbQuery($sql, $bindArr, $conn, $stmt);
            return $conn->lastInsertId();
        }

        protected function dbQuery($sql, $bindArr, &$conn, &$stmt)
        {
            $instance = DBClass::getInstance();
            $conn = $instance->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute($bindArr);
            return $conn->lastInsertId();
        }
    }