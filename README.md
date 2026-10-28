# PAM Native i18n

Plugin opcional de catálogos, tradução e formatação para PAM Native. Somente PHP,
sem dependências nativas, permissões, timers, polling ou requisições de rede.

```sh
composer require pushinbr/pam-native-i18n:^0.1
```

Requer PHP 8.5 e PAM Native ^1.46. O autoload do Composer registra `t()` no
gancho genérico dos cores extraídos. No PAM 1.46 anterior à extração, instala
um alias antecipado para o tradutor que o `t()` embutido chama; por isso, carregue
`vendor/autoload.php` antes de registrar catálogos.

```php
use Pam\Native\I18n\Translator;
use Pam\Native\I18n\{Format, DateStyle};
use function Pam\Native\I18n\{t, useLocale, useTimezone};

Translator::source('en');
Translator::catalog('en', ['common' => [
    'hello' => 'Hello {name}',
    'items_one' => '{count} item',
    'items_other' => '{count} items',
]]);
Translator::catalog('pt', static fn (): array => require __DIR__.'/locales/pt.php');
Translator::use('en-US');
echo t('common.hello', ['name' => 'Ana']);
echo t('common.items', ['count' => 1234]);
// Templates: {{ t('common.hello', ['name' => 'Ana']) }}

$locale = useLocale();
$locale->set('pt-BR');
$locale->set(null); // volta ao locale do dispositivo
$zone = useTimezone();
$zone->set('America/Sao_Paulo');
echo Format::date('2026-10-12', DateStyle::Medium, 'UTC');
```

`Translator::use()` escolhe o catálogo ativo; `null` restaura o catálogo fonte.
Para traduzir pelo sistema, escolha `Translator::use(\Pam\Native\useLocale()->value)`
no boot. O `useLocale()` do plugin expõe o locale efetivo, resolução de catálogo,
região, valor do dispositivo, `known`, `overridden` e `set()`.
O composable do core sempre expõe somente o sistema e não tem `set()`.

Catálogos usam namespaces e chaves planas (`common.hello`). Suportam fallback,
`{placeholders}`, plurais `_zero`, `_one` e `_other`, regras customizadas e listas
separadas por `|`. Um catálogo pode ser array ou loader lazy. O cache de loaders
retém no máximo o catálogo fonte e um catálogo traduzido; uma troca pode recarregar
um catálogo descartado. `reset()` limpa catálogos e preferências.

Os dois composables compartilham uma fonte de dispositivo e uma assinatura de
preferências. A assinatura acaba quando o último escopo desmonta. O host é
consultado no primeiro acesso e na volta ao foreground; não há polling.

`Format` preserva as APIs de número, contagem, compacto, data, hora, relativo,
bytes e duração. `Format::useLocale()` define sua preferência de formatação,
independente da escolha de catálogo. Os enums `DateStyle` e `RelativeTimeStyle`
preservam seus valores inteiros sequenciais.

Compatibilidade: no core extraído, `Pam\Native\I18n\I18n` é alias de `Translator`;
`Pam\Native\Support\{Format, DateStyle, RelativeTimeStyle}` são aliases das classes
do plugin. Métodos `t()` do próprio componente mantêm precedência nos templates.

Testes sem instalar dependências adicionais:

```sh
PAM_NATIVE_ROOT=/caminho/do/pam-native php tests/run.php
```

Ou, após instalar dependências compatíveis, `composer test`. A suíte cobre catálogos,
fallback, plurais, interpolação, loaders, formatação, templates, shims e ciclo de
vida dos composables. O core tem uma suíte separada que roda sem o plugin.
