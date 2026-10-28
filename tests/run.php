<?php

declare(strict_types=1);

$GLOBALS['i18nAssertions'] = 0;
$assert = require __DIR__.'/bootstrap.php';
require __DIR__.'/catalogs.php';
require __DIR__.'/format.php';
require __DIR__.'/locale.php';
require __DIR__.'/compatibility.php';
echo 'I18N_PLUGIN_OK assertions='.$GLOBALS['i18nAssertions']."\n";
