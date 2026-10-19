# TaskDay — Gerenciador de tarefas com Laravel Scheduler

O **TaskDay** é uma aplicação web de gerenciamento de tarefas desenvolvida com **Laravel 12**. Ela permite cadastrar tarefas, marcá-las como concluídas, visualizá-las em uma lixeira e removê-las definitivamente. O projeto também demonstra o uso do **Laravel Scheduler** para executar rotinas automáticas: alternar o tema visual da aplicação e apagar tarefas que estão na lixeira há mais de 30 dias.

O repositório utiliza o nome **Scheduler**, enquanto a interface exibe a marca **TaskDay**.

## Funcionalidades

- **Cadastro de tarefas:** inclusão de uma tarefa por meio do título, com validação obrigatória e limite de 255 caracteres.
- **Listagem de tarefas ativas:** exibição de todas as tarefas ainda não concluídas.
- **Conclusão de tarefas:** o botão **Marcar como feita** aplica *soft delete*, movendo a tarefa para a lixeira sem apagar imediatamente o registro do banco.
- **Lixeira:** exibe as tarefas concluídas, o total de itens e a data/hora em que cada tarefa foi excluída logicamente.
- **Exclusão definitiva manual:** o botão **Excluir** remove permanentemente uma tarefa da lixeira.
- **Limpeza automática:** uma rotina agendada apaga definitivamente tarefas que estão na lixeira há **30 dias ou mais**, todos os dias às **18h20**.
- **Tema global claro/escuro:** o agendador atualiza o tema armazenado no cache e a página apresenta um indicador visual de dia ou noite.
- **Dados de exemplo:** o *seeder* disponibiliza tarefas ativas e tarefas com diferentes datas de exclusão para facilitar os testes.

## Tecnologias utilizadas

| Tecnologia | Utilização |
| --- | --- |
| PHP 8.2+ | Linguagem do back-end |
| Laravel 12 | Framework, rotas, controllers, Eloquent e agendamento |
| MySQL | Banco de dados configurado no `.env.example` |
| Blade | Renderização das páginas |
| Bootstrap 5 / Bootswatch United | Estilização carregada por CDN |
| Laravel Scheduler | Execução de comandos e limpeza agendada |
| Eloquent SoftDeletes | Exclusão lógica de tarefas |
| Cache do Laravel | Armazenamento do tema global |
| Vite 7 e Tailwind CSS 4 | Ferramentas de front-end configuradas no projeto |
| Pest 3 | Estrutura de testes automatizados |

> **Nota:** embora Vite e Tailwind estejam configurados, o layout atual carrega o CSS do Bootswatch diretamente pela CDN e não inclui a diretiva `@vite` para carregar os arquivos compilados.

## Pré-requisitos

Antes de iniciar, tenha instalado:

- **PHP 8.2 ou superior**, com as extensões necessárias ao Laravel e `pdo_mysql` para usar MySQL;
- **Composer**;
- **Node.js e npm** (para os recursos de desenvolvimento/front-end configurados);
- **MySQL** em execução;
- Um terminal para executar os comandos Artisan.

## Instalação e configuração

### 1. Obter o projeto

Baixe o repositório e abra a pasta onde estão o arquivo `artisan` e o `composer.json`.

### 2. Instalar as dependências

```bash
composer install
npm install
```

### 3. Criar o arquivo de ambiente

No Linux/macOS:

```bash
cp .env.example .env
```

No Prompt de Comando do Windows:

```cmd
copy .env.example .env
```

### 4. Configurar o banco de dados

Crie um banco MySQL para o projeto, por exemplo:

```sql
CREATE DATABASE taskday CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Depois, ajuste as configurações correspondentes no arquivo `.env`:

```dotenv
APP_NAME=TaskDay
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=America/Sao_Paulo

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskday
DB_USERNAME=root
DB_PASSWORD=sua_senha

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

Substitua `DB_USERNAME` e `DB_PASSWORD` pelas credenciais de seu MySQL. Caso o usuário não tenha senha, utilize `DB_PASSWORD=`.

**Atenção:** o `.env.example` original contém `QUEUE_CONNECTION=file`, mas não há uma conexão de filas chamada `file` em `config/queue.php`. Altere para `database` (ou `sync`, caso não utilize um trabalhador de filas). Confira também se as variáveis `DB_*` estão sem espaços antes de seus nomes.

### 5. Gerar a chave da aplicação e criar as tabelas

```bash
php artisan key:generate
php artisan migrate
```

Para adicionar as tarefas de demonstração:

```bash
php artisan db:seed
```

Também é possível executar migrações e *seeders* de uma vez, em um banco ainda não preparado:

```bash
php artisan migrate --seed
```

### 6. Iniciar o servidor

```bash
php artisan serve
```

Abra **http://127.0.0.1:8000** no navegador.

Para desenvolver os recursos CSS/JavaScript configurados no Vite, execute separadamente:

```bash
npm run dev
```

O projeto também possui `npm run build` para gerar os arquivos de produção. Atualmente, esses arquivos não são referenciados pelo layout Blade principal.

## Ativando as tarefas agendadas

**O agendamento não começa automaticamente ao executar `php artisan serve`.** Para que as rotinas de tema e limpeza sejam executadas no ambiente de desenvolvimento, abra outro terminal na pasta do projeto e rode:

```bash
php artisan schedule:work
```

Comandos úteis:

```bash
# Ver os eventos agendados
php artisan schedule:list

# Executar agora os eventos agendados para o minuto atual
php artisan schedule:run

# Alterar o tema manualmente para escuro
php artisan theme:toggle dark

# Alterar o tema manualmente para claro
php artisan theme:toggle light
```

Em produção, configure o agendador do sistema para chamar o comando abaixo a cada minuto, na pasta do projeto:

```cron
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

### Rotinas configuradas

| Frequência | Rotina | Comportamento |
| --- | --- | --- |
| A cada minuto | `theme:toggle dark` | Define o tema global como escuro |
| A cada 2 minutos | `theme:toggle light` | Define o tema global como claro |
| Diariamente, às 18h20 | Limpeza de tarefas | Exclui permanentemente tarefas com `deleted_at` de 30 dias ou mais |

As duas rotinas de tema podem coincidir no mesmo minuto; como estão declaradas nessa ordem, o comando de tema claro será executado depois do escuro quando ambos forem acionados. O tema é **global**, não uma preferência individual por usuário, e fica armazenado na chave de cache `global_theme`. A interface usa **light** como padrão quando a chave não existe.

## Como utilizar

**Criar tarefa:** na seção **Nova Tarefa**, digite um título e clique em **Adicionar Tarefa**. Ela aparecerá na lista de tarefas ativas.

**Concluir tarefa:** clique em **Marcar como feita**. A tarefa deixará a lista principal e aparecerá em **Lixeira/Tarefas resolvidas** com a data da exclusão lógica.

**Excluir definitivamente:** na lixeira, clique em **Excluir** ao lado da tarefa desejada. Essa operação apaga o registro em vez de apenas alterar `deleted_at`.

**Limpeza automática:** mantenha o Scheduler ativo. Quando uma tarefa completar 30 dias na lixeira, ela poderá ser removida na próxima execução diária da limpeza, às 18h20, conforme o horário da aplicação.

## Estrutura do projeto

```text
scheduler-main/
├── app/
│   ├── Console/Commands/
│   │   └── UpdateGlobalTheme.php       # Comando Artisan para mudar o tema
│   ├── Http/Controllers/
│   │   └── TaskController.php          # Listagem, cadastro e exclusões
│   └── Models/
│       ├── Task.php                    # Modelo com SoftDeletes
│       └── User.php                    # Modelo padrão de usuário do Laravel
├── bootstrap/
│   └── app.php                         # Inicialização e configuração de rotas
├── config/                              # Configurações do Laravel
├── database/
│   ├── factories/
│   │   └── TaskFactory.php             # Geração de tarefas fictícias
│   ├── migrations/                      # Tabelas de usuários, jobs, cache e tarefas
│   └── seeders/
│       ├── DatabaseSeeder.php          # Executa o TaskSeeder
│       └── TaskSeeder.php              # Tarefas para demonstração
├── public/
│   └── images/day_and_night.svg        # Imagem do tema dia/noite
├── resources/
│   ├── css/app.css                     # Entrada CSS do Tailwind
│   ├── js/app.js                       # Entrada JavaScript
│   └── views/
│       ├── index.blade.php             # Página principal de tarefas
│       └── layouts/main-layout.blade.php # Estrutura visual da página
├── routes/
│   ├── web.php                         # Rotas HTTP
│   └── console.php                     # Agendamentos do Scheduler
├── tests/                               # Testes Pest de exemplo
├── .env.example                         # Modelo de variáveis de ambiente
├── composer.json                        # Dependências PHP e scripts
├── package.json                         # Dependências e scripts Node.js
└── vite.config.js                       # Configuração do Vite
```

## Rotas disponíveis

| Método | Rota | Nome | Finalidade |
| --- | --- | --- | --- |
| `GET` | `/` | `tasks.index` | Mostrar tarefas ativas e tarefas na lixeira |
| `POST` | `/tasks` | `tasks.store` | Criar uma nova tarefa |
| `POST` | `/tasks/{task}` | `tasks.destroy` | Mover uma tarefa ativa para a lixeira |
| `POST` | `/tasks/{id}/force-delete` | `tasks.force-delete` | Remover uma tarefa definitivamente |

As operações de escrita são feitas por formulários Blade com proteção CSRF (`@csrf`). O projeto **não implementa uma API REST** separada.

## Banco de dados

A migração `2026_10_14_142018_create_tasks_table.php` define a tabela principal `tasks`:

| Campo | Tipo | Descrição |
| --- | --- | --- |
| `id` | ID autoincremental | Identificador da tarefa |
| `title` | String | Título da tarefa |
| `created_at` | Timestamp | Data de criação |
| `updated_at` | Timestamp | Última atualização |
| `deleted_at` | Timestamp nullable | Data da exclusão lógica; nulo para tarefas ativas |

O modelo `Task` utiliza `HasFactory` e `SoftDeletes`. O título está listado em `$fillable`, permitindo o cadastro por atribuição em massa.

### Dados inseridos pelo seeder

Executando `php artisan db:seed` em um banco vazio, o `TaskSeeder` cria:

- **4 tarefas ativas**;
- **2 tarefas** com `deleted_at` definido para aproximadamente **5 dias atrás**;
- **3 tarefas** com `deleted_at` definido para aproximadamente **45 dias atrás**.

As 3 tarefas mais antigas ficam elegíveis para exclusão na rotina diária do Scheduler. Rodar o seeder novamente adiciona novos registros; ele não limpa automaticamente os anteriores.

Além de `tasks`, as migrações padrão do Laravel incluem tabelas para usuários, redefinição de senha, sessões, cache e filas. A presença dessas tabelas não significa que o sistema já ofereça login ou cadastro de usuários na interface.

## Testes

O projeto usa **Pest** e possui testes de exemplo nas pastas `tests/Feature` e `tests/Unit`.

Para executar a suíte:

```bash
php artisan test
```

A configuração de testes em `phpunit.xml` utiliza **SQLite em memória** e `CACHE_STORE=array`. O teste de página inicial acessa `/`, que consulta a tabela `tasks`; portanto, é necessário garantir que as migrações sejam executadas no banco de testes. Os arquivos de teste incluídos ainda não cobrem cadastro, exclusão lógica, exclusão definitiva ou tarefas agendadas.

## Comandos rápidos

```bash
composer install                 # Instalar dependências PHP
npm install                      # Instalar dependências Node.js
php artisan migrate --seed       # Criar tabelas e dados de exemplo
php artisan serve                # Iniciar aplicação local
php artisan schedule:work        # Executar agendador no desenvolvimento
php artisan schedule:list        # Listar tarefas programadas
php artisan schedule:run         # Rodar eventos devidos no minuto atual
php artisan theme:toggle dark    # Definir tema escuro
php artisan theme:toggle light   # Definir tema claro
php artisan test                 # Rodar testes Pest
npm run dev                      # Iniciar Vite
npm run build                    # Compilar arquivos front-end
```

O `composer.json` também fornece o script `composer run dev`, que tenta iniciar servidor, trabalhador de filas, logs e Vite em paralelo. Ele **não inicia `schedule:work`**; portanto, o Scheduler precisa de um processo próprio.

## Observações sobre a versão atual

- O sistema utiliza apenas o **título** das tarefas. A tela contém uma referência opcional a `description`, mas a migração não cria essa coluna e o modelo não permite preenchê-la.
- Não há funcionalidade para **editar tarefas** ou **restaurar** itens da lixeira.
- A barra **Search** no cabeçalho é visual e não possui uma busca funcional implementada nas rotas.
- Não há fluxo de **autenticação** conectado às páginas, apesar de existir a estrutura padrão de usuários do Laravel.
- O CSS do Bootswatch é obtido pela internet; a disponibilidade desse arquivo depende do CDN. O layout também não inclui o JavaScript do Bootstrap, necessário para alguns componentes interativos, como o menu recolhível.
- A interface não mostra explicitamente as mensagens `success` gravadas na sessão pelo controller.
- O funcionamento das rotinas agendadas depende de um processo executando o Scheduler e da configuração correta de fuso horário e cache.

---

**TaskDay / Scheduler** — projeto de demonstração de CRUD parcial de tarefas, exclusão lógica e automações com Laravel Scheduler.
