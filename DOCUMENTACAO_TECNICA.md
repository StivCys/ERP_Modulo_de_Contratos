# Documentação Técnica

## 1. Estrutura da Aplicação
A aplicação foi estruturada utilizando o framework Laravel no backend e Vue.js 3 (via Inertia.js) no frontend. Para garantir a manutenibilidade e escalabilidade, adotamos uma arquitetura baseada em **Domain-Driven Design (DDD)** simplificado no backend. 

Os domínios estão localizados no diretório `app/Domains/`, onde cada módulo possui seu próprio escopo e responsabilidades encapsuladas. O frontend, por sua vez, está estruturado na pasta `resources/js/` contendo páginas (Pages) e componentes reutilizáveis.

## 2. Organização das Camadas
Optamos por abster a estrutura padrão do MVC do Laravel (onde as regras de negócio ficam presas entre `app/Http/Controllers` e `app/Models`) em favor de uma separação mais lógica voltada a casos de uso:

A pasta `app/Domains/` contém os subdiretórios dos domínios (ex: `Cliente`, `Contrato`, `Servico`). Dentro de cada domínio, temos:
- **Models:** Entidades de Domínio e relação com o banco de dados.
- **Controllers/API:** Requisições de entrada, validação inicial e acionamento de Casos de Uso (Actions/Services).
- **Requests:** Classes FormRequest para validação rigorosa dos dados de entrada.
- **Services/Actions:** Onde a regra de negócio realmente reside. Classes de serviço ou ações com responsabilidade única.
- **Rules/Contracts:** Definição de interfaces e regras de negócio granulares (ex: Padrão *Strategy* para as regras de precificação de contratos).

## 3. Decisões Técnicas Tomadas
- **Inertia.js com Vue 3:** Escolhido para eliminar a complexidade de construir uma API REST exclusiva para o SPA e lidar com autenticação independentemente na criação da UI, agilizando o desenvolvimento enquanto mantém a reatividade de um fluxo SPA.
- **Domain-Driven Design (DDD):** Adotado para evitar modelos "gordos" (Fat Models) e controllers "inflados" (Fat Controllers), separando as responsabilidades de forma clara e facilitando os testes de unidade.
- **Laravel Octane:** Configurado para lidar com conexões persistentes, garantindo alta performance caso o projeto entre em ambiente produtivo escalável.
- **Padrão Strategy para Regras de Cálculo:** Utilizado para calcular o valor de um contrato dinamicamente, permitindo adicionar novas regras (como descontos por volume e taxas adicionais) sem alterar o core do serviço de cálculo (`ContratoCalculoService`).

## 4. Implementação das Regras de Negócio
As regras de precificação dinâmica de contratos foram encapsuladas em classes que implementam a interface `RegraContratoInterface`. O `ContratoCalculoService` recebe o contrato, itera sobre uma cadeia de regras injetadas e calcula os valores de descontos, acréscimos e o preço final, baseando-se nos itens do contrato.

Esta abordagem respeita o principio OCP (Open-Closed Principle) do SOLID: para adicionar uma nova regra (ex: "Desconto de Black Friday"), basta criar uma nova classe que implemente a interface e registrá-la, sem a necessidade de modificar a lógica principal de faturamento.

Também implementamos um **módulo de Auditoria/Histórico**, que intercepta as mutações sobre os Contratos (via actions), rastreando a autoria e os snapshots do estado, permitindo a exibição de uma timeline clara para o usuário final no frontend.

## 5. O Que Melhoraria Com Mais Tempo
Se houvesse mais tempo para evolução do projeto, os seguintes pontos poderiam ser refinados:
1. **Design System / Componentização Frontend:** Criar uma biblioteca de componentes de UI mais agnóstica (ex: Storybook) para padronizar formulários, tabelas e botões em todo o painel, reduzindo duplicação de código Vue/CSS.
2. **Cobertura de Testes End-to-End (E2E):** Implementar testes de integração com Cypress ou Laravel Dusk para automatizar validações de fluxos principais no navegador, indo além dos testes unitários já realizados no ecossistema do backend.
3. **Repository Pattern:** Dependendo do crescimento das consultas ao banco de dados, abstrair consultas e scopes mais pesados em Repositórios, desonerando responsabilidades de *Query Builder* das actions atuais.
4. **Filas (Queues) para Histórico/Eventos:** Processos como a consolidação de *history logs* ou envio de eventuais e-mails de notificação ao cliente poderiam ser despachados para processamento em *background* (Filas no Redis), o que deixaria o tempo de resposta das requisições principais de cadastro / edição mais rápido.
5. **Automação de CI/CD:** Implementação de pipelines automatizados (Ex: GitHub Actions) aplicando `PHPStan`, `Pint`, Execução do `PHPUnit` e deploy dinâmico.
