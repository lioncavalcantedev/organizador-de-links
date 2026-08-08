# Instruções do projeto

## Idioma

* Responda sempre em português do Brasil.
* Escreva mensagens de commit em português.
* Use inglês somente quando o projeto ou um padrão técnico exigir.

## Forma de trabalho

Antes de modificar arquivos:

1. analise o código relacionado;
2. explique o que pretende fazer;
3. apresente um plano;
4. informe os arquivos que serão criados ou modificados;
5. aguarde minha aprovação.

Depois da aprovação:

* altere somente os arquivos necessários;
* preserve a arquitetura e os padrões existentes;
* execute os testes relacionados;
* informe os testes executados e seus resultados;
* apresente um resumo das alterações;
* informe pendências, limitações ou riscos encontrados;
* não declare que algo funciona sem uma verificação adequada.

## Laravel

* Siga as convenções e a arquitetura do Laravel.
* Priorize os recursos nativos do framework.
* Não introduza camadas, abstrações ou padrões sem benefício claro.
* Utilize Form Requests para validações complexas ou reutilizáveis.
* Utilize migrations para alterações na estrutura do banco de dados.
* Utilize policies ou gates para autorização quando necessário.
* Evite regras de negócio extensas em controllers.
* Não realize consultas ao banco diretamente nas views.

## Código

* Priorize código simples, legível e fácil de manter.
* Mantenha responsabilidades bem definidas.
* Evite duplicação de código.
* Utilize tipagem quando compatível com o projeto.
* Siga o padrão de código já adotado e a PSR-12 quando aplicável.
* Não introduza novas dependências sem justificar.
* Comente apenas trechos importantes ou pouco óbvios.
* Não altere arquivos fora do escopo solicitado.

## Segurança e banco de dados

* Valide os dados de entrada.
* Considere autenticação e autorização em operações protegidas.
* Considere riscos de SQL Injection, XSS, CSRF e exposição de dados.
* Não exponha credenciais nem registre informações sensíveis.
* Não desative mecanismos de segurança para facilitar implementações.
* Não apague dados, tabelas ou colunas sem autorização explícita.
* Antes de migrations destrutivas, explique os riscos e proponha uma estratégia segura.
* Considere índices, integridade referencial e impacto das consultas.

## Testes

* Execute os testes relacionados às alterações.
* Prefira testes Feature para fluxos da aplicação.
* Utilize testes Unit para regras isoladas quando fizer sentido.
* Crie testes para correções de bugs quando possível.
* Não modifique testes para ocultar falhas reais.
* Informe claramente quando algum teste não puder ser executado.

## Commits

Utilize Conventional Commits em português:

* `feat: adiciona cadastro de usuário`
* `fix: corrige validação de senha`
* `refactor: reorganiza serviço de autenticação`
* `test: adiciona testes para login`
* `docs: atualiza documentação da API`
* `chore: atualiza configurações do projeto`

Antes de sugerir uma mensagem de commit:

* analise as alterações atuais;
* considere somente o conteúdo presente no diff;
* escolha o tipo que representa a alteração principal;
* não execute o commit sem autorização explícita.

## Dúvidas e decisões

Quando um requisito não estiver claro:

* faça perguntas antes de implementar;
* não invente regras de negócio.

Quando houver mais de uma solução válida:

* apresente as principais alternativas;
* explique os impactos relevantes;
* recomende a solução mais adequada ao contexto do projeto.
