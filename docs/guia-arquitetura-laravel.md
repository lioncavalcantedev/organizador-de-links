# Guia de arquitetura para projetos Laravel

## Objetivo

Este documento define uma arquitetura simples e sustentável para aplicações web
monolíticas construídas com Laravel. Ele pode ser copiado para outros projetos e
adaptado conforme o domínio da aplicação.

A arquitetura prioriza os recursos nativos do Laravel e evita a criação de
camadas adicionais sem uma necessidade concreta. O objetivo é manter o código
legível, testável e fácil de evoluir.

## Tecnologias de referência

A versão de cada tecnologia deve ser definida de acordo com os requisitos do
projeto. Como referência, esta arquitetura pode ser utilizada com:

- PHP 8.2 ou superior;
- Laravel 12;
- Eloquent ORM;
- Blade e Blade Components;
- Vite;
- Pest ou PHPUnit;
- Tailwind CSS e uma biblioteca de componentes, quando necessários.

As ferramentas de interface podem ser substituídas sem alterar a arquitetura do
backend.

## Princípios

- Usar as convenções e os recursos nativos do Laravel.
- Manter controllers pequenos e focados na coordenação do fluxo HTTP.
- Validar toda entrada recebida pela aplicação.
- Autorizar operações sobre recursos protegidos.
- Usar Eloquent para persistência e relacionamentos.
- Não consultar o banco de dados diretamente nas views.
- Representar alterações no banco por migrations.
- Criar testes para fluxos importantes e correções de bugs.
- Evitar Services, Repositories, Actions e DTOs quando não trouxerem benefício
  claro.
- Não expor informações sensíveis em respostas, logs ou arquivos versionados.

## Visão geral

A aplicação segue uma arquitetura MVC baseada na estrutura do Laravel:

```text
requisição HTTP
    → rota
    → middleware
    → Form Request
    → autorização
    → controller
    → model Eloquent
    → banco de dados
    → view Blade ou redirecionamento
```

Cada camada deve ter uma responsabilidade bem definida. Regras que pertencem ao
domínio não devem ser espalhadas por controllers, views ou arquivos de rotas.

## Estrutura recomendada

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Auth/
│   └── Requests/
├── Models/
├── Policies/
├── Rules/
└── Providers/

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
└── views/
    ├── components/
    └── layouts/

routes/
├── console.php
└── web.php

tests/
├── Feature/
└── Unit/
```

Pastas adicionais devem ser criadas somente quando houver uma responsabilidade
real que não se encaixe adequadamente nessa estrutura.

## Responsabilidades

### Rotas

As rotas definem:

- URL;
- método HTTP;
- controller responsável;
- nome da rota;
- middleware aplicável.

Elas não devem conter regras de negócio nem consultas complexas.

Organize as rotas de acordo com o nível de acesso:

```php
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Cadastro e autenticação de visitantes.
});

Route::middleware('auth')->group(function () {
    Route::resource('items', ItemController::class);
});

// Rotas públicas devem ficar depois das rotas específicas.
```

Use os métodos HTTP conforme a intenção da operação:

| Operação | Método |
| --- | --- |
| Listar ou exibir | `GET` |
| Criar | `POST` |
| Substituir | `PUT` |
| Alterar parcialmente | `PATCH` |
| Excluir | `DELETE` |

Operações que alteram estado, como logout, não devem usar `GET`.

### Controllers

Controllers coordenam o fluxo da requisição:

1. recebem dados já validados;
2. verificam a autorização;
3. executam uma operação no domínio;
4. retornam uma resposta, view ou redirecionamento.

Exemplo:

```php
namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;

class ItemController extends Controller
{
    public function store(StoreItemRequest $request)
    {
        $request->user()
            ->items()
            ->create($request->validated());

        return to_route('items.index')
            ->with('message', 'Item cadastrado com sucesso.');
    }
}
```

Evite controllers com validações manuais, consultas duplicadas ou regras
extensas. Quando um fluxo crescer, identifique primeiro se a regra pertence a um
model, a uma Policy ou a outro recurso nativo do Laravel.

### Form Requests

Use Form Requests quando a validação for complexa, reutilizável ou fizer parte de
um fluxo relevante:

```bash
php artisan make:request StoreItemRequest
```

Exemplo:

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'url' => ['nullable', 'url'],
        ];
    }
}
```

Use `$request->validated()` para obter somente os dados validados. Regras de
negócio extensas e operações de persistência normalmente não devem ficar no Form
Request.

Quando uma validação própria for necessária, crie uma Rule:

```bash
php artisan make:rule NomeDaRegra
```

### Models

Models representam as entidades persistidas e seus relacionamentos. Eles também
podem conter comportamentos diretamente relacionados à entidade.

Exemplo de relacionamento:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'name',
        'url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

No model relacionado:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

public function items(): HasMany
{
    return $this->hasMany(Item::class);
}
```

Prefira declarar `$fillable` ou `$guarded` em cada model. Não desative
globalmente a proteção contra mass assignment.

Consultas reutilizáveis podem ser representadas por local scopes. Regras que
envolvem vários contextos, integrações ou processos complexos podem justificar
uma classe específica.

### Policies

Policies determinam se um usuário pode executar uma operação sobre um recurso:

```bash
php artisan make:policy ItemPolicy --model=Item
```

Exemplo:

```php
namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function update(User $user, Item $item): bool
    {
        return $item->user()->is($user);
    }

    public function delete(User $user, Item $item): bool
    {
        return $item->user()->is($user);
    }
}
```

A autorização pode ser executada pelo controller:

```php
$this->authorize('update', $item);
```

Ou por middleware na rota:

```php
Route::put('/items/{item}', [ItemController::class, 'update'])
    ->middleware('can:update,item');
```

Não confie apenas em esconder botões na interface. A autorização deve ser
validada no backend.

### Migrations

Toda alteração estrutural no banco deve ser realizada por uma migration:

```bash
php artisan make:migration create_items_table
```

Exemplo:

```php
Schema::create('items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();
    $table->string('name');
    $table->string('url')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'created_at']);
});
```

Ao definir a estrutura:

- use chaves estrangeiras para preservar a integridade;
- crie índices para consultas frequentes;
- use restrições `unique` também no banco;
- defina campos opcionais como `nullable`;
- avalie cuidadosamente exclusões em cascata;
- não remova tabelas ou colunas sem uma estratégia segura.

Migrations destrutivas exigem avaliação de impacto, backup e estratégia de
implantação compatível com o ambiente.

### Factories e seeders

Factories devem produzir registros válidos e servir aos testes:

```bash
php artisan make:factory ItemFactory --model=Item
```

Seeders podem fornecer dados úteis para o ambiente local:

```bash
php artisan make:seeder ItemSeeder
```

Não use credenciais reais nem dados pessoais em factories ou seeders.

### Views e Blade Components

As views cuidam da apresentação. Elas podem:

- mostrar dados recebidos pelo controller;
- exibir erros de validação;
- renderizar formulários;
- reutilizar Blade Components;
- verificar permissões para controlar a interface.

Elas não devem:

- consultar diretamente o banco;
- realizar persistência;
- conter regras de negócio;
- decidir sozinhas se uma ação é autorizada.

Organize elementos reutilizáveis em `resources/views/components`:

```blade
<x-button type="submit">Salvar</x-button>
<x-input name="name" :value="old('name')" />
```

Mantenha um layout comum em `resources/views/layouts` ou no padrão de
componentes de layout já adotado pelo projeto.

## Autenticação

Priorize os recursos nativos do Laravel:

- guard de sessão para aplicações web;
- middleware `auth` e `guest`;
- `Auth::attempt()` para autenticação por credenciais;
- regeneração da sessão após login;
- invalidação da sessão e regeneração do token CSRF após logout;
- cast `hashed` para senhas.

Exemplo de autenticação:

```php
use Illuminate\Support\Facades\Auth;

if (Auth::attempt($request->validated())) {
    $request->session()->regenerate();

    return redirect()->intended(route('dashboard'));
}

return back()->withErrors([
    'email' => 'As credenciais informadas são inválidas.',
]);
```

Exemplo de logout:

```php
Auth::logout();

$request->session()->invalidate();
$request->session()->regenerateToken();

return to_route('login');
```

## Upload de arquivos

Para uploads:

- valide tipo, tamanho e extensões aceitas;
- gere nomes seguros;
- armazene pelo filesystem do Laravel;
- evite confiar no nome enviado pelo usuário;
- remova o arquivo anterior somente após armazenar o novo com sucesso;
- não exponha caminhos internos;
- avalie se os arquivos devem ser públicos ou privados.

Exemplo de regras:

```php
'photo' => [
    'nullable',
    'image',
    'mimes:jpg,jpeg,png,webp',
    'max:2048',
],
```

Exemplo de armazenamento:

```php
$path = $request->file('photo')->store('photos', 'public');
```

## Transações e integridade

Use transações quando uma operação alterar vários registros que precisam ser
confirmados ou desfeitos em conjunto:

```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () use ($item, $otherItem) {
    $item->update(['position' => $otherItem->position]);
    $otherItem->update(['position' => $item->getOriginal('position')]);
});
```

Também trate explicitamente condições de borda, como a ausência de um registro
anterior ou seguinte durante uma reordenação.

## Tratamento de erros

- Use exceções e respostas nativas do Laravel.
- Não revele stack traces em produção.
- Apresente mensagens úteis sem expor detalhes internos.
- Evite capturar exceções apenas para ignorá-las.
- Registre somente as informações necessárias para diagnóstico.
- Não registre senhas, tokens, credenciais ou dados pessoais desnecessários.

## Testes

### Testes Feature

Use testes Feature para fluxos HTTP e integração com o framework:

- autenticação e logout;
- acesso autenticado e acesso negado;
- validação de formulários;
- criação, edição e exclusão;
- autorização sobre recursos de outros usuários;
- upload de arquivos;
- redirecionamentos e mensagens de sessão;
- persistência e integridade do banco.

Exemplo com Pest:

```php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('usuário autenticado pode cadastrar um item', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('items.store'), [
            'name' => 'Item de exemplo',
            'url' => 'https://example.com',
        ])
        ->assertRedirect(route('items.index'));

    $this->assertDatabaseHas('items', [
        'user_id' => $user->id,
        'name' => 'Item de exemplo',
    ]);
});
```

Exemplo de autorização:

```php
test('usuário não pode alterar item de outro usuário', function () {
    $user = User::factory()->create();
    $item = Item::factory()->for(User::factory())->create();

    $this->actingAs($user)
        ->put(route('items.update', $item), [
            'name' => 'Alterado',
        ])
        ->assertForbidden();
});
```

### Testes Unit

Use testes Unit para regras isoladas que não dependem do ciclo HTTP, como:

- cálculos;
- transformações;
- objetos de valor;
- validações próprias;
- algoritmos de ordenação;
- regras de domínio isoláveis.

Não force uma regra a ser Unit quando um teste Feature representar melhor o
comportamento real.

### Execução

```bash
php artisan test
```

Ou, se o projeto possuir o script correspondente:

```bash
composer test
```

Execute ao menos os testes diretamente relacionados após cada alteração.

## Quando criar Services ou Actions

Não adicione uma camada apenas por convenção. Considere uma classe de serviço ou
ação quando:

- o mesmo caso de uso for chamado por HTTP, fila e console;
- o fluxo coordenar várias entidades ou integrações;
- uma operação exigir transação e várias etapas;
- o controller continuar extenso após mover validação e autorização;
- a regra não pertencer naturalmente a um único model.

Uma Action pode representar um caso de uso:

```text
app/Actions/CreateItem.php
```

Um Service pode encapsular uma integração ou capacidade específica:

```text
app/Services/PaymentGateway.php
```

Repositories geralmente não são necessários quando o Eloquent já atende às
consultas e à persistência. Adote-os somente quando houver um benefício
mensurável, como múltiplas fontes de dados ou isolamento exigido pelo domínio.

## Processo para implementar uma funcionalidade

Siga esta sequência:

1. Descreva o comportamento esperado e as regras de negócio.
2. Identifique entidades, relacionamentos e permissões.
3. Crie ou ajuste migrations.
4. Atualize models e relacionamentos.
5. Crie factories para os dados necessários aos testes.
6. Escreva testes Feature para o fluxo principal e cenários de erro.
7. Crie Form Requests e Rules.
8. Crie ou atualize Policies.
9. Implemente controllers e rotas.
10. Implemente views e componentes.
11. Execute os testes relacionados.
12. Execute o formatador adotado pelo projeto.
13. Revise segurança, consultas e tratamento de erros.

## Checklist de revisão

### Estrutura

- [ ] A alteração segue a estrutura existente do Laravel?
- [ ] Cada classe possui uma responsabilidade clara?
- [ ] Alguma abstração foi criada sem necessidade concreta?
- [ ] Há código duplicado que deveria ser reutilizado?

### Entrada e segurança

- [ ] Todos os dados de entrada são validados?
- [ ] Dados protegidos passam por autorização no backend?
- [ ] Os formulários possuem proteção CSRF?
- [ ] Operações de alteração usam métodos HTTP adequados?
- [ ] Mass assignment está restrito no model?
- [ ] Arquivos enviados possuem validação de tipo e tamanho?
- [ ] Informações sensíveis estão ausentes dos logs e respostas?

### Banco de dados

- [ ] Alterações estruturais foram feitas por migrations?
- [ ] Chaves estrangeiras e regras de exclusão são adequadas?
- [ ] Restrições únicas importantes também existem no banco?
- [ ] As consultas frequentes possuem índices apropriados?
- [ ] Operações com múltiplas escritas usam transação quando necessário?

### Apresentação

- [ ] As views não consultam o banco?
- [ ] Componentes Blade são reutilizados quando apropriado?
- [ ] A saída está protegida contra XSS?
- [ ] Erros de validação são apresentados corretamente?

### Testes

- [ ] O fluxo principal possui teste Feature?
- [ ] Cenários de validação e autorização foram testados?
- [ ] Uma correção de bug possui teste de regressão?
- [ ] Os testes relacionados foram executados?
- [ ] O resultado dos testes foi registrado no resumo da alteração?

## Comandos úteis

```bash
# Criar um projeto Laravel
composer create-project laravel/laravel nome-do-projeto "^12.0"

# Criar model, migration e factory
php artisan make:model Item -mf

# Criar controller resource
php artisan make:controller ItemController --resource --model=Item

# Criar Form Requests
php artisan make:request StoreItemRequest
php artisan make:request UpdateItemRequest

# Criar Policy
php artisan make:policy ItemPolicy --model=Item

# Criar teste Pest
php artisan make:test ItemTest --pest

# Executar migrations
php artisan migrate

# Executar testes
php artisan test

# Formatar código PHP
./vendor/bin/pint
```

## Registro das decisões arquiteturais

Este guia define a base, mas decisões específicas do projeto devem ser
registradas. Para decisões relevantes, documente:

- contexto e problema;
- alternativas consideradas;
- decisão escolhida;
- consequências e limitações;
- data da decisão.

Exemplo:

```markdown
## ADR-001: armazenamento de imagens

### Contexto

A aplicação precisa armazenar fotos enviadas pelos usuários.

### Decisão

Usar o filesystem do Laravel com o disco configurado por ambiente.

### Consequências

O ambiente local pode usar armazenamento público, enquanto produção pode usar um
serviço de objetos compatível.
```

## Adaptação ao novo domínio

Antes de começar um projeto, substitua os exemplos genéricos deste documento
pelos conceitos reais do domínio. Registre ao menos:

- entidades principais;
- relacionamentos;
- papéis de usuário;
- operações permitidas por papel;
- dados públicos e privados;
- integrações externas;
- requisitos de auditoria;
- regras de retenção ou exclusão;
- riscos técnicos conhecidos.

Este documento deve evoluir junto com a aplicação. Quando a implementação e o
guia divergirem, revise a decisão e atualize ambos de forma consciente.
