<?php

declare(strict_types=1);

use Pam\Native\Internal\TemplateExpression;
use Pam\Native\I18n\Translator;

TemplateExpression::registerFunction('t', static function (mixed ...$arguments): string {
    if (count($arguments) < 1 || count($arguments) > 2 || !is_string($arguments[0])
        || (isset($arguments[1]) && !is_array($arguments[1]))) {
        throw new RuntimeException('Template t() expects a message key and an optional parameter array.');
    }

    return Translator::t($arguments[0], $arguments[1] ?? []);
});
