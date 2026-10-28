<?php

declare(strict_types=1);

use Pam\Native\Internal\TemplateExpression;
use Pam\Native\I18n\Translator;

// PAM 1.46 predates the generic helper registry. Its built-in t() calls this
// legacy class name; install the alias before the app registers any catalogs.
// Extracted cores use their MovedToPlugin shim and the registry below instead.
if (!method_exists(TemplateExpression::class, 'registerFunction')) {
    if (class_exists('Pam\\Native\\I18n\\I18n', false)) {
        if (!is_a('Pam\\Native\\I18n\\I18n', Translator::class, true)) {
            throw new LogicException('Load pam-native-i18n through Composer before registering catalogs.');
        }
    } else {
        class_alias(Translator::class, 'Pam\\Native\\I18n\\I18n');
    }
    return;
}

TemplateExpression::registerFunction('t', static function (mixed ...$arguments): string {
    if (count($arguments) < 1 || count($arguments) > 2 || !is_string($arguments[0])
        || (isset($arguments[1]) && !is_array($arguments[1]))) {
        throw new RuntimeException('Template t() expects a message key and an optional parameter array.');
    }

    return Translator::t($arguments[0], $arguments[1] ?? []);
});
