<?php

declare(strict_types=1);

namespace Pam\Native\I18n;

if (!\function_exists('Pam\Native\I18n\useLocale')) {
    /** `->value` ("pt-BR"), `->language`, `->region`, `->device`, `->known`, `->overridden`, `->set(?string)`. */
    function useLocale(): LocaleState
    {
        $device = \Pam\Native\Composables\Sources::device();
        $device->acquireForScope();
        $overrides = Sources::i18n();
        $overrides->acquireForScope();

        return new LocaleState($device, $overrides);
    }
}

if (!\function_exists('Pam\Native\I18n\useTimezone')) {
    /** `->value` ("America/Sao_Paulo"), `->device`, `->known`, `->overridden`, `->zone()`, `->now()`, `->set(?string)`. */
    function useTimezone(): TimezoneState
    {
        $device = \Pam\Native\Composables\Sources::device();
        $device->acquireForScope();
        $overrides = Sources::i18n();
        $overrides->acquireForScope();

        return new TimezoneState($device, $overrides);
    }
}

/** @param array<string, scalar|null> $params */
function t(string $key, array $params = []): string
{
    return Translator::t($key, $params);
}
