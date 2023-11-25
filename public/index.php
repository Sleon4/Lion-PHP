<?php

declare(strict_types=1);
// -----------------------------------------------------------------------------
header('Content-Type: application/json');
// -----------------------------------------------------------------------------
require_once(__DIR__ . '/../vendor/autoload.php');
// -----------------------------------------------------------------------------
include_once(__DIR__ . '/../routes/rules.php');
include_once(__DIR__ . '/../routes/middleware.php');
include_once(__DIR__ . '/../routes/web.php');
