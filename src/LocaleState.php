<?php

declare(strict_types=1);

namespace Pam\Native\I18n;

use Pam\Native\Composables\{ReactiveObject, SharedSource};
use Pam\Native\System\DeviceInfo;

/**
 * `useLocale()`: the device language, with the app's Translator::use() override on
 * top. Reactive: changes when the override changes or when the app returns
 * to the foreground with another system language.
 *
 * @property-read string $value effective BCP 47 tag ("pt-BR"): the override, else the device, else the I18n source locale
 * @property-read string $language catalog language for that tag (Translator::resolve(), e.g. "pt")
 * @property-read ?string $region region subtag ("BR"), null when the tag has none
 * @property-read string $device system language tag, '' until the host reported it
 * @property-read bool $known true once the host reported the device locale
 * @property-read bool $overridden true while Translator::use() overrides the device
 */
final class LocaleState extends ReactiveObject
{
    /**
     * @param SharedSource<DeviceInfo|null> $device
     * @param SharedSource<array{0: ?string, 1: ?string}> $overrides
     */
    public function __construct(
        private readonly SharedSource $device,
        private readonly SharedSource $overrides,
    ) {
    }

    protected function properties(): array
    {
        return ['value', 'language', 'region', 'device', 'known', 'overridden'];
    }

    protected function read(string $name): mixed
    {
        \Pam\Native\Composables\Sources::requestDevice();
        $info = $this->device->signal->get();
        [$override] = $this->overrides->signal->get();
        $device = $info?->locale ?? '';
        $value = $override ?? ($device !== '' ? $device : Translator::sourceLocale());

        return match ($name) {
            'value' => $value,
            'language' => Translator::resolve($value),
            'region' => self::region($value),
            'device' => $device,
            'known' => $info !== null,
            default => $override !== null,
        };
    }

    /** Overrides the language (Translator::use()); null follows the device again. */
    public function set(?string $locale): void
    {
        Translator::use($locale);
    }

    private static function region(string $tag): ?string
    {
        foreach (array_slice(preg_split('/[-_]/', $tag) ?: [], 1) as $part) {
            if (preg_match('/^(?:[A-Za-z]{2}|\d{3})$/D', $part) === 1) return strtoupper($part);
        }

        return null;
    }
}
