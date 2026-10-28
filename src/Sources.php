<?php

declare(strict_types=1);

namespace Pam\Native\I18n;

use Pam\Native\Composables\SharedSource;

/** @internal Scoped and shared override subscription. */
final class Sources
{
    /**
     * Locale and time zone overrides (Translator::use(), Translator::useTimezone()).
     *
     * @return SharedSource<array{0: ?string, 1: ?string}>
     */
    public static function i18n(): SharedSource
    {
        return SharedSource::named(
            'i18n',
            static fn (): array => [Translator::preferredLocale(), Translator::timezone()],
            static function (\Closure $push): \Closure {
                $id = Translator::onChange(static fn () => $push([Translator::preferredLocale(), Translator::timezone()]));

                return static fn () => Translator::offChange($id);
            },
        );
    }
}
