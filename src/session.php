<?php
if (session_status() === PHP_SESSION_NONE) {
        if (extension_loaded('redis')) {
        @ini_set('session.save_handler', 'redis');
        @ini_set('session.save_path', 'tcp://redis:6379');
    }

    session_start();
}