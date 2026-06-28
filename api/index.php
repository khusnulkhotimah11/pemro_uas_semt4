<?php

// Override script name to prevent CodeIgniter 4 from stripping the "/api" folder prefix from URIs on Vercel
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Forward request to the CodeIgniter 4 public index.php entry point
require __DIR__ . '/../backend-api/public/index.php';
