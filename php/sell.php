<?php
/**
 * Legacy/wrong URL guard – sell form lives at /sell.php (project root).
 */
require_once __DIR__ . '/../includes/helpers.php';
redirect_to('sell.php');
