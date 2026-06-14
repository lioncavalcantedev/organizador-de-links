# Instruções do projeto — Organizador de Links

Aplicação Laravel para organização de links.

## Stack

- **PHP** `^8.3`
- **Laravel** `^13.8`
- **Banco de dados:** SQLite (`database/database.sqlite`)
- **Frontend:** Blade + Vite + Tailwind CSS `^4.0`
- **Formatação:** Laravel Pint
- **Testes:** PHPUnit `^12` (com Mockery e Collision)
- **DX:** Laravel Tinker, Pail (logs) e Pao

## Comandos

Use os scripts do Composer sempre que possível:

- `composer dev` — sobe servidor, worker de fila, logs (Pail) e Vite simultaneamente
- `composer test` — limpa config e roda a suíte de testes
- `composer setup` — instala dependências, gera key, migra e builda assets
- `php artisan migrate` — aplica migrations
- `./vendor/bin/pint` — formata o código (rode antes de finalizar)
- `npm run dev` / `npm run build` — assets via Vite

## Convenções de código

- Siga o **PSR-12** e mantenha o código formatado com **Pint** (não invente um estilo próprio).
- Use **tipagem estrita**: type hints em parâmetros, retornos e propriedades.
- Aproveite recursos modernos do Laravel 13 e do PHP 8.3 (ex.: PHP attributes como `#[Fillable]` / `#[Hidden]` nos models, conforme já usado em `app/Models/User.php`).
- Use **Eloquent** para acesso a dados; evite SQL cru sem necessidade.
- **Form Requests** para validação; **Resources** para serialização de respostas de API.
- Nomes: classes em `StudlyCase`, métodos/variáveis em `camelCase`, tabelas/colunas em `snake_case`.
- Não adicione comentários óbvios; comente apenas intenção não trivial.

## Estrutura

- `app/Models/` — models Eloquent
- `app/Http/Controllers/` — controllers
- `app/Providers/` — service providers
- `routes/web.php` — rotas web; `routes/console.php` — comandos artisan
- `database/migrations/` — migrations · `database/factories/` — factories · `database/seeders/` — seeders
- `resources/views/` — templates Blade · `resources/css` e `resources/js` — assets
- `config/` — configuração · `tests/` — testes (`Unit` e `Feature`)

## Testes

- Escreva testes com PHPUnit em `tests/Feature` (fluxos) e `tests/Unit` (lógica isolada).
- Use **factories** para gerar dados; o ambiente de teste usa banco isolado.
- Rode `composer test` antes de concluir qualquer mudança relevante.

## Boas práticas

- Nunca commite segredos. O `.env` é ignorado pelo git — edite `.env.example` quando adicionar novas variáveis.
- Crie uma migration para qualquer mudança de schema (nunca edite o banco manualmente).
- Mantenha a lógica de negócio fora dos controllers (services/actions) quando crescer.
- Rode `pint` e `composer test` antes de finalizar.
