# ERP (Módulo de Contratos)

## Descrição do projeto
O projeto trata-se de um sistema ERP focado na gestão de contratos, clientes e prestação de serviços. Ele permite a criação de contratos com diferentes serviços, aplicando regras de negócio dinâmicas como descontos por volume e acréscimos para serviços específicos.

## Funcionalidades implementadas
- **Gestão de Clientes:** CRUD completo de clientes (Nome, E-mail, CPF/CNPJ, Status).
- **Gestão de Serviços:** Cadastro e manutenção de serviços prestados.
- **Gestão de Contratos:** Criação, edição e exclusão de contratos associados a clientes.
- **Regras de Precificação Dinâmica:** Sistema flexível de cálculo do valor do contrato, incluindo descontos progressivos e taxas adicionais baseadas em serviços específicos.
- **Histórico e Auditoria:** Rastreabilidade das alterações nos contratos (criação, atualização de itens, etc.).
- **API RESTful:** Endpoints protegidos para consumo externo.

## Tecnologias utilizadas
- **Backend:** PHP 8.2, Laravel 11, Laravel Octane, Sanctum (Autenticação).
- **Frontend:** Vue.js 3, Inertia.js, Tailwind CSS.
- **Banco de Dados:** MySQL (via Docker).
- **Infraestrutura:** Docker & Docker Compose.
- **Arquitetura:** Domain-Driven Design (DDD) simplificado.

---

# Instalação do projeto

# 1. Configurar ambiente
```bash
cp .env.example .env
``` 

# Editar:

```.env
APP_URL=http://<SEU-IP-OU-localhost>:8000
VITE_DEV_SERVER_URL=http://<SEU-IP-OU-localhost>:5173

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

# SE USAR IP PRECISA CONFIGURAR O vite.config.js TAMBEM !!!

```javascript
    export default defineConfig({
  server: {
    host: '0.0.0.0', // <- importante (não usar IP fixo aqui)
    port: 5173,
    strictPort: true,
    hmr: {
      host: 'SEU_IP_AQUI',
    },
  },
});
```

# 2. Buildar a imagem

```bash
make build
# ou: docker compose build
```

# 3. Subir os containers (Octane ou PHP-FPM)

Para executar com **Laravel Octane + Swoole**:
```bash
make octane
```

Para executar com **Nginx + PHP-FPM**:
```bash
make fpm
```

A alternância entre os dois modos é imediata e preserva todos os dados do banco e dependências.

Comandos de apoio:
```bash
make logs   # Visualizar logs em tempo real
make down   # Parar os containers com segurança
```

# 4. Acessar: http://localhost:8000 ou http://<SEU-IP>:8000


# 5. Acesso
 Login:     test@example.com 

 senha:     password

---

## API REST

A API REST está disponível em `/api` e utiliza autenticação via **Laravel Sanctum** (tokens Bearer).

### Autenticação

#### 1. Obter token de acesso

```http
POST /api/tokens/create
Content-Type: application/json

{
    "email": "test@example.com",
    "password": "password",
    "device_name": "meu-app"
}
```

**Resposta:**
```json
{
    "token": "1|abc123xyz..."
}
```

Use o token retornado no header `Authorization` de todas as requisições protegidas:

```
Authorization: Bearer 1|abc123xyz...
```

#### 2. Revogar token

```http
DELETE /api/tokens/revoke
Authorization: Bearer {token}
```

---

### Endpoints disponíveis

Todos os endpoints abaixo requerem `Authorization: Bearer {token}`.

#### Usuário autenticado

```http
GET /api/user
```

---

#### Clientes

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/clientes` | Listar (paginado) |
| POST | `/api/clientes` | Criar |
| GET | `/api/clientes/{id}` | Buscar por ID |
| PUT | `/api/clientes/{id}` | Atualizar |
| DELETE | `/api/clientes/{id}` | Remover |

**Body para criação:**
```json
{
    "nome": "Nome do Cliente",
    "email": "cliente@email.com",
    "cpf_cnpj": "123.456.789-00",
    "ativo": "sim"
}
```

---

#### Contratos

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/contratos` | Listar (paginado) |
| POST | `/api/contratos` | Criar |
| GET | `/api/contratos/{id}` | Buscar com histórico |
| PUT | `/api/contratos/{id}` | Atualizar |
| DELETE | `/api/contratos/{id}` | Remover |

**Body para criação:**
```json
{
    "cliente_id": 1,
    "data_inicio": "2026-01-01",
    "data_fim": "2026-12-31",
    "status": "ativo",
    "items": [
        {
            "servico_id": 1,
            "quantidade": 2
        }
    ]
}
```

**Body para atualização** (inclua `id` dos items existentes para preservar histórico):
```json
{
    "cliente_id": 1,
    "data_inicio": "2026-01-01",
    "data_fim": "2026-12-31",
    "status": "ativo",
    "items": [
        {
            "id": 5,
            "servico_id": 1,
            "quantidade": 3
        },
        {
            "servico_id": 2,
            "quantidade": 1
        }
    ]
}
```

---

### Criação de usuário e token via Tinker

```bash
php artisan tinker
```

```php
$user = App\Models\User::first();
$token = $user->createToken('api-test')->plainTextToken;
echo $token;
```


# Testes

Apos rodar os testes o usuario adm pode perder as permissoes devido ao refreshdatabase, 
entao será preciso entrar no container e
rodar o o arquivo assign-role.sh para atribuir as permissoes ao usuario adm

```bash
./assign-role.sh
``` 