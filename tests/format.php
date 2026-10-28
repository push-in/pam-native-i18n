<?php

declare(strict_types=1);

use Pam\Native\I18n\{Format, DateStyle, RelativeTimeStyle};

// Format and Text.
$assert(
    Format::count(1234567) === '1.234.567' && Format::count(1234567, 'en') === '1,234,567'
        && Format::countLabel(1, 'item', 'itens') === '1 item' && Format::countLabel(1234, 'item', 'itens') === '1.234 itens',
    'Format::count must group thousands per locale.',
);
$assert(
    Format::compact(999) === '999' && Format::compact(1000) === '1 mil' && Format::compact(1234) === '1,2 mil'
        && Format::compact(12_345) === '12 mil' && Format::compact(1_250_000) === '1,3 mi' && Format::compact(2_000_000_000) === '2 bi'
        && Format::compact(-1500) === '-1,5 mil' && Format::compact(999_999) === '1 mi',
    'Format::compact must abbreviate pt-BR magnitudes and roll over units.',
);
$assert(
    Format::compact(1234, 'en') === '1.2K' && Format::compact(1_500_000, 'en') === '1.5M'
        && Format::compact(1990, truncate: true) === '1,9 mil' && Format::compact(12_345, truncate: true, decimalsBelow: 1_000) === '12,3 mil'
        && Format::compact(9_999, plainBelow: 10_000) === '9.999' && Format::compact(15_900, truncate: true) === '15 mil',
    'Format::compact options must cover truncation, plain ranges and decimals.',
);
$base = 1_760_000_000;
$assert(
    Format::relative($base, $base + 30) === 'agora' && Format::relative($base, $base + 300) === 'há 5 min'
        && Format::relative($base, $base + 7_200) === 'há 2 h' && Format::relative($base, $base + 3 * 86_400) === 'há 3 d'
        && Format::relative($base, $base + 14 * 86_400) === 'há 2 sem' && Format::relative($base, $base - 50) === 'agora'
        && Format::relative('', $base) === '' && Format::relative('not a date', $base) === '',
    'Format::relative short pt-BR must follow the feed wording.',
);
$assert(
    Format::relative($base, $base + 60, RelativeTimeStyle::Long) === 'há 1 minuto'
        && Format::relative($base, $base + 7_200, RelativeTimeStyle::Long) === 'há 2 horas'
        && Format::relative($base, $base + 300, RelativeTimeStyle::Narrow) === '5 min'
        && Format::relative($base, $base + 300, locale: 'en') === '5m ago'
        && Format::relative($base, $base + 7_200, RelativeTimeStyle::Long, 'en') === '2 hours ago'
        && Format::relative($base, $base + 7_200, RelativeTimeStyle::Narrow, 'en') === '2h',
    'Format::relative must support long, narrow and English wording.',
);
$october = (new DateTimeImmutable('2025-10-12 15:00:00', new DateTimeZone('UTC')))->getTimestamp();
$assert(
    Format::relative($october, $october + 60 * 86_400, timeZone: 'UTC') === '12 out.'
        && Format::relative($october, $october + 400 * 86_400, timeZone: 'UTC') === '12 out. 2025'
        && Format::relative($october, $october + 8 * 86_400, RelativeTimeStyle::Long, maxWeeks: 0, timeZone: 'UTC') === '12 de outubro'
        && Format::relative($october, $october + 60 * 86_400, locale: 'en', timeZone: 'UTC') === 'Oct 12'
        && Format::relative('2025-10-12T15:00:00Z', $october + 400 * 86_400, RelativeTimeStyle::Long, 'en', timeZone: 'UTC') === 'October 12, 2025',
    'Old times must fall back to a calendar date with the year when it differs.',
);
$assert(
    Format::bytes(512) === '512 B' && Format::bytes(1_536) === '1,5 KB' && Format::bytes(12 * 1_048_576) === '12 MB'
        && Format::bytes(1_048_576) === '1 MB' && Format::bytes(1_288_490_189, 'en') === '1.2 GB' && Format::bytes(-5) === '0 B',
    'Format::bytes must use binary units with one decimal below ten.',
);
$assert(
    Format::duration(7) === '0:07' && Format::duration(65.4) === '1:05' && Format::duration(3_723) === '1:02:03'
        && Format::duration(65, padMinutes: true) === '01:05' && Format::duration(59.7, floor: true) === '0:59' && Format::duration(-3) === '0:00',
    'Format::duration must render media clocks.',
);
$assert(
    Format::time('2026-10-14T17:05:00Z', 'America/Sao_Paulo') === '14:05' && Format::time($october, 'UTC', 'en') === '3:00 PM'
        && Format::time('', 'UTC') === '',
    'Format::time must render the clock in the given zone and locale.',
);
$assert(
    Format::relative($base, $base + 300, RelativeTimeStyle::Tiny) === '5m' && Format::relative($base, $base + 3 * 86_400, RelativeTimeStyle::Tiny) === '3d'
        && Format::relative($base, $base + 14 * 86_400, RelativeTimeStyle::Tiny) === '2sem'
        && Format::relative($base, $base + 14 * 86_400, RelativeTimeStyle::Tiny, 'en') === '2w'
        && Format::relative($base, $base + 40 * 86_400, RelativeTimeStyle::Tiny, maxDays: PHP_INT_MAX) === '40d'
        && Format::relative($october, $october + 9 * 86_400, RelativeTimeStyle::Tiny, maxWeeks: 0, timeZone: 'UTC', dateStyle: DateStyle::Medium) === '12 de out.'
        && Format::relative($base, $base + 300, timeZone: 'Not/AZone') === 'há 5 min'
        && Format::relative($october, $october + 60 * 86_400, timeZone: 'Not/AZone') === '',
    'Tiny style, day/week limits, date fallback style and invalid zones must be honoured.',
);
$assert(
    Format::date($october, DateStyle::Short, 'UTC', now: $october) === '12 out.'
        && Format::date($october, DateStyle::Medium, 'UTC', now: $october) === '12 de out.'
        && Format::date($october, DateStyle::Long, 'UTC', now: $october + 400 * 86_400) === '12 de outubro de 2025'
        && Format::date($october, DateStyle::Numeric, 'UTC') === '12/10'
        && Format::date($october, DateStyle::NumericWithYear, 'UTC') === '12/10/2025'
        && Format::date($october, DateStyle::Numeric, 'UTC', 'en') === '10/12'
        && Format::date($october, DateStyle::Medium, 'UTC', 'en', withYear: true) === 'Oct 12, 2025'
        && Format::date($october - 7 * 86_400, DateStyle::Medium, 'UTC', now: $october, padDay: true) === '05 de out.'
        && Format::date('', DateStyle::Medium) === '' && Format::time($october, 'Not/AZone') === '',
    'Format::date must render calendar dates per style and locale.',
);
$assert(
    Format::date($october, DateStyle::NumericWithShortYear, 'UTC') === '12/10/25'
        && Format::dateTime($october, timeZone: 'UTC') === '12/10/2025 15:00'
        && Format::dateTime($october, DateStyle::Medium, ', ', 'UTC', withYear: false) === '12 de out., 15:00'
        && Format::relative($october - 7 * 86_400, $october + 9 * 86_400, RelativeTimeStyle::Tiny, maxWeeks: 0, timeZone: 'UTC', dateStyle: DateStyle::Medium, padDay: true, withYear: false) === '05 de out.',
    'Short-year dates, dateTime() and the relative date fallback options must work.',
);
Format::useLocale('en-US');
$assert(Format::compact(1234) === '1.2K' && Format::locale() === 'en-US', 'useLocale() must change the default locale.');
Format::useLocale('pt-BR');
