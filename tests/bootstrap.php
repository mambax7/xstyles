<?php declare(strict_types=1);

// Core-dependent checks run in isolated subprocesses; never bootstrap a live site here.
require_once __DIR__ . '/Support/ScriptTestCase.php';
