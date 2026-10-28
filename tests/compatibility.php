<?php

declare(strict_types=1);

use Pam\Native\I18n\{Translator, Format, DateStyle, RelativeTimeStyle};
use Pam\Native\Plugin\MovedToPlugin;
use Pam\Native\Internal\TemplateExpression;
use Pam\Native\Composables\Sources;
use function Pam\Native\I18n\t;

$assert(class_exists('Pam\\Native\\I18n\\I18n') && is_a('Pam\\Native\\I18n\\I18n', Translator::class, true), 'Legacy I18n is a plugin alias.');
$assert(class_exists('Pam\\Native\\Support\\Format') && is_a('Pam\\Native\\Support\\Format', Format::class, true), 'Legacy Format is the same class.');
$assert(\Pam\Native\Support\DateStyle::Medium === DateStyle::Medium, 'DateStyle cases retain identity.');
$assert(\Pam\Native\Support\RelativeTimeStyle::Tiny === RelativeTimeStyle::Tiny, 'RelativeTimeStyle cases retain identity.');
$assert(count(array_filter(MovedToPlugin::aliased(), static fn (string $package): bool => $package === 'pushinbr/pam-native-i18n')) === 4, 'All four shims are recorded.');
Translator::source('pt');
Translator::catalog('pt', ['common' => ['hello' => 'Olá {name}']]);
Translator::use('pt');
$assert(t('common.hello', ['name' => 'Ana']) === 'Olá Ana', 'PHP t() matches catalogs.');
foreach ([true, false] as $generated) {
    TemplateExpression::useGeneratedCode($generated);
    foreach (["t()", "t(1)", "t('x', 1)", "t('x', [], 1)"] as $expression) {
        $invalid = false;
        try { TemplateExpression::evaluate($expression, null, []); }
        catch (RuntimeException) { $invalid = true; }
        $assert($invalid, 'Template t() validates arguments.');
    }
}
TemplateExpression::useGeneratedCode(true);
// Plugin preferences never change the core's system-only composable.
Sources::reset();
$system = \Pam\Native\useLocale();
Translator::use('fr-CA');
$assert($system->value === 'en', 'Core locale ignores translation preferences.');
Translator::reset();
Sources::reset();

// A long-lived process switching languages retains at most two loaded catalogs.
Translator::source('en');
foreach (['en', 'pt', 'fr'] as $language) {
    Translator::catalog($language, static fn (): array => ['common' => ['hello' => $language]]);
}
$loaded = new ReflectionProperty(Translator::class, 'loaded');
foreach (['pt', 'fr', 'pt', 'fr'] as $language) {
    Translator::use($language);
    $assert(Translator::t('common.hello') === $language, 'Switching lazy catalogs preserves translations.');
    Translator::t('common.missing');
    $assert(count($loaded->getValue()) <= 2, 'Catalog loader cache stays bounded across language changes.');
}
Translator::reset();
$assert(\Pam\Native\Composables\SharedSource::active() === [], 'Plugin teardown releases every shared subscription.');
