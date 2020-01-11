<?php
    spl_autoload_register(function ($className) {
        $file = 'models/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });