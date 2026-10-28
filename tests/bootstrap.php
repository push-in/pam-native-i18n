<?php

declare(strict_types=1);

if (!defined('PAM_I18N_TEST_BOOTSTRAPPED')) {
    define('PAM_I18N_TEST_BOOTSTRAPPED', true);
    $core = getenv('PAM_NATIVE_ROOT') ?: dirname(__DIR__).'/vendor/pushinbr/pam-native';
    if (!is_file($core.'/packages/native/src/functions.php')) throw new RuntimeException('Install dependencies or set PAM_NATIVE_ROOT.');
    spl_autoload_register(static function (string $class) use ($core): void {
        foreach (['Pam\\Native\\I18n\\' => dirname(__DIR__).'/src/', 'Pam\\Native\\' => $core.'/packages/native/src/'] as $prefix => $directory) {
            if (!str_starts_with($class, $prefix)) continue;
            $path = $directory.str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
            if (is_file($path)) { require_once $path; return; }
        }
    });
    require_once $core.'/packages/native/src/Polyfill/mbstring_functions.php';
    require_once $core.'/packages/native/tests/arch_v2_bootstrap.php';
    require_once dirname(__DIR__).'/src/functions.php';
    require_once dirname(__DIR__).'/src/bootstrap.php';
}
return static function (bool $condition, string $message): void {
    ++$GLOBALS['i18nAssertions'];
    if (!$condition) throw new RuntimeException($message);
};
