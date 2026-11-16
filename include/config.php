<?php
// Use deployment environment variables. No shared signing secret is provided.
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: '');
define('DB_USER', getenv('DB_USER') ?: '');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('JWT_SECRET', getenv('JWT_SECRET') ?: '');
if (strlen(JWT_SECRET) < 32) {
    throw new RuntimeException('Set JWT_SECRET to a unique random value of at least 32 characters.');
}
define('SMS_APIKEY', getenv('YUNPIAN_API_KEY') ?: '');
define('SMS_SIGN', getenv('YUNPIAN_SMS_SIGN') ?: '');
session_start();
