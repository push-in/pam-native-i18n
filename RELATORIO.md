# Relatório u3 — plugin i18n

## O que foi feito

- Publicado `pushinbr/pam-native-i18n` 0.1.0 em https://github.com/push-in/pam-native-i18n/releases/tag/v0.1.0 e https://packagist.org/packages/pushinbr/pam-native-i18n.
- Extraídos catálogos, tradução, pluralização, interpolação, listas, preferências reativas de locale/fuso e Format/DateStyle/RelativeTimeStyle.
- PHP puro; sem permissões, módulos nativos, polling ou downloads de catálogos. O cache retém no máximo dois catálogos carregados.
- Bootstrap compatível com o core extraído (registro genérico de helpers) e com PAM 1.46 anterior à extração (alias antecipado do tradutor legado).
- Assinaturas compartilhadas são liberadas no fim do último escopo. Enums preservam os valores inteiros sequenciais.

## Commits

- `2a1594e`: inicialização do repositório.
- `d8e115c`: implementação, documentação e testes.
- `d1044f6`: compatibilidade de boot com PAM 1.46, commit publicado na tag v0.1.0.

## Validadores — 2/2

- Suíte `tests/run.php`: 70 asserções aprovadas contra o core extraído; inclui aliases, cache limitado e liberação de assinaturas.
- `composer validate --strict`: aprovado.
- A release/tag e os metadados do Packagist foram consultados depois da publicação.

## Pendências

- QA visual do real-world app depende de disponibilidade do emulador compartilhado; nenhum módulo nativo foi alterado neste plugin.
