# MeuERP

Sistema ERP completo desenvolvido com Laravel 12, Vue 3, Inertia.js, Tailwind CSS e PrimeVue.

## Stack Tecnológica

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Vue 3, Inertia.js 2.0, Tailwind CSS 4, PrimeVue 4.5
- **Banco de Dados:** MySQL 8+
- **Autenticação:** Laravel Jetstream (2FA, Teams, Profile Photos)
- **Permissões:** Spatie Permission
- **Build:** Vite 7

## Módulos

| Módulo | Descrição |
|--------|-----------|
| Dashboard | Visão geral com KPIs e alertas de estoque baixo |
| Empresas | Cadastro multi-empresa |
| Clientes | Cadastro com CPF/CNPJ |
| Fornecedores | Cadastro com CNPJ |
| Categorias | Categorização de produtos |
| Produtos | Catálogo com preço de custo/venda, SKU, estoque mínimo |
| Estoque | Movimentações (entrada/saída/ajuste) |
| Compras | Pedidos de compra com confirmação e geração de conta a pagar |
| Vendas | Pedidos de venda com validação de estoque e geração de conta a receber |
| Contas a Pagar | Controle de pagamentos a fornecedores |
| Contas a Receber | Controle de recebimentos de clientes |

## Regras de Negócio

### Compras
- Compra inicia como **Rascunho**
- Ao **confirmar**: estoque é incrementado, conta a pagar é gerada automaticamente
- Ao **cancelar**: estoque é revertido

### Vendas
- Venda inicia como **Rascunho**
- Ao **confirmar**: valida estoque disponível, decrementa estoque, gera conta a receber
- Ao **cancelar**: estoque é devolvido

### Estoque
- Movimentações de **Entrada**, **Saída** e **Ajuste** (define valor absoluto)
- Registro de saldo após cada movimentação
- Alerta de estoque baixo no dashboard

## Instalação

### Pré-requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+
- XAMPP ou equivalente

### Passos

```bash
# Clonar o repositório
git clone https://github.com/lambjulia/MeuERP MeuERP
cd MeuERP

# Instalar dependências PHP
composer install

# Instalar dependências JavaScript
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate
```

Configurar o `.env` com as credenciais do MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meuerp
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# Criar o banco de dados
mysql -u root -e "CREATE DATABASE meuerp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Executar migrations e seeders
php artisan migrate --seed

# Compilar assets
npm run dev
```

### Acesso

Após o seed, acesse com:
- **URL:** http://localhost/MeuERP/public
- **Email:** admin@meuerp.com
- **Senha:** password

## Desenvolvimento

```bash
# Servidor Laravel
php artisan serve

# Vite dev server
npm run dev

# Rodar testes
php artisan test

# Rodar apenas testes de feature
php artisan test --filter=PurchaseTest
php artisan test --filter=SaleTest
php artisan test --filter=StockMovementTest
```

## Estrutura do Projeto

```
app/
├── Actions/          # Business logic (Confirm, Cancel, Adjust)
├── Enums/            # PurchaseStatus, SaleStatus, FinancialStatus, StockMovementType
├── Http/
│   ├── Controllers/  # Resource controllers
│   ├── Middleware/    # HandleInertiaRequests (flash messages)
│   └── Requests/     # Form validation
├── Models/           # Eloquent models with relationships
resources/js/
├── Components/       # Base reusable components (BasePageHeader, BaseMoney, etc.)
├── Composables/      # useMoney, useDate
├── Enums/            # Frontend enum definitions
├── Layouts/          # ErpLayout with sidebar navigation
└── Pages/            # Vue pages organized by module
```

## Licença

Proprietário - Todos os direitos reservados.
