# Magento Sou Energy - Módulo de Autenticação, Notificações e Termos de Uso

Projeto desenvolvido em **Magento 2 Open Source (2.4.7-p3)** com foco na implementação de um sistema modular e desacoplado de autenticação, área restrita de usuário comum, central de notificações e gestão de termos de uso, além de interface administrativa exclusiva e preparação completa para consumo por frontend externo em **Vue.js**.

---

## 🛠️ Stack Tecnológica e Versões

Ambiente homologado e testado:

| Software                | Versão Utilizada         | Função no Projeto                                                                         |
| :---------------------- | :----------------------- | :---------------------------------------------------------------------------------------- |
| **PHP**                 | `8.3.x`                  | Backend runtime (extensões: `intl`, `pdo_mysql`, `bcmath`, `soap`, `sodium`, `gd`, `zip`) |
| **Composer**            | `2.11.x`                 | Gerenciador de dependências PHP                                                           |
| **MySQL**               | `8.0+ / 26.x (Homebrew)` | Banco de dados relacional principal                                                       |
| **OpenSearch**          | `2.x`                    | Mecanismo de busca e indexação exigido pelo Magento 2.4+                                  |
| **Magento Open Source** | `2.4.7-p3`               | Plataforma backend, modularidade EAV e exposição de APIs                                  |
| **Node.js**             | `v23.x`                  | Ambiente de execução para o frontend desacoplado                                          |
| **NPM**                 | `10.x`                   | Gerenciador de pacotes do frontend                                                        |
| **Vue.js**              | `3.x`                    | Frontend SPA desacoplado (Fase 4)                                                         |

---

## 🔗 Acessos e Credenciais

### 1. Painel Administrativo
- **URL:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
- **Usuário:** `admin`
- **Senha:** `AdminPassword123*`

### 2. Usuário Comum (Loja / Storefront)
- **URL Base:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
- **Login:** [http://127.0.0.1:8000/customer/account/login/](http://127.0.0.1:8000/customer/account/login/)
- **E-mail:** `randel@souenergy.com.br`
- **Senha:** `UserPassword123*`
- **Nome:** Marcos Randel  
*(Usuário provisionado automaticamente via Data Patch oficial do Magento).*

---

## 📦 Módulo Desenvolvido: `SouEnergy_Notification`

O módulo foi implementado em `app/code/SouEnergy/Notification/` seguindo a arquitetura limpa do Magento 2, dividido em três domínios funcionais:

### 1. Usuário Comum e Área do Cliente
- **Interface Minimalista:** A barra lateral da conta do cliente foi simplificada para exibir exclusivamente:
  - **My Account** (dados cadastrais essenciais do usuário).
  - **🔔 Minhas Notificações** (central unificada de avisos).
  - Remoção de todos os menus de e-commerce tradicionais (pedidos, avaliações, listas de desejos, cartões de crédito e comparadores de produtos).
- **Atributos de Auditoria EAV:** Novos atributos vinculados ao cliente:
  - `terms_version`: Versão do termo aceita pelo usuário (ex: `v1.0`, `v2.0`).
  - `terms_accepted_at`: Data e hora exatas do aceite (timestamp).
- **Indexação em Tempo Real:** O indexador `customer_grid` opera em modo *Realtime (Update on Save)*, refletindo o aceite do cliente no painel Admin instantaneamente.

### 2. Central de Notificações
- **Tabela Dedicada:** Estruturada via `db_schema.xml` (`souenergy_notification`) com índices em `customer_id` e `is_read`.
- **Destinatários Flexíveis:** Suporte tanto para notificações direcionadas a um cliente específico quanto gerais (broadcast para todos).
- **Ações Dinâmicas:** Campos opcionais de **Link de Ação (URL)** (`action_url`) e **Texto do Botão** (`action_label`), permitindo redirecionar para termos de uso, configurações ou qualquer rota desejada.
- **Sino Interativo 🔔:** Integrado ao cabeçalho da loja, exibindo em tempo real o contador numérico de notificações não lidas.
- **Marcação de Leitura:** Opção no frontend para marcar notificações pendentes como lidas.

### 3. Gestão de Termos de Uso (Conformidade LGPD)
- **Painel de Gestão:** Submenu dedicado no Admin para visualização da versão vigente e publicação de novas versões.
- **Automação de Disparo:** Ao salvar uma nova versão dos termos, o sistema **dispara automaticamente uma notificação para toda a base de clientes** com o link direto para a tela de leitura e confirmação.
- **Auditoria Visual no Grid de Clientes:** A coluna de termos na listagem de usuários comuns exibe dinamicamente a versão vigente no cabeçalho e classifica cada linha com badges de status:
  - `✓ v2.0 (Atualizado)`: Usuário já aceitou a versão vigente.
  - `⚠️ Desatualizado (v1.0)`: Usuário aceitou uma versão anterior.
  - `❌ Não Aceitou`: Usuário novo ou com aceite pendente.
- **Tela de Termos com Confirmação:** Interface detalhada com o texto legal e botão de aceite inteligente que registra o consentimento no perfil do usuário.

---

## 🎛️ Painel Administrativo Exclusivo (Menu Sou Energy)

Para manter o painel limpo e focado no escopo do teste, todos os menus padrão de e-commerce da Adobe (Catálogo, Vendas, Marketing, Conteúdo, etc.) foram ocultados, restando o menu corporativo:

```
Sou Energy
 ├── 👥 Usuários Comuns      -> Listagem de clientes com status de aceite dos termos
 ├── 🔔 Notificações         -> Gestão e envio manual de notificações
 └── 📄 Termos de Uso        -> Controle da versão vigente e disparo automático
```

---

## ⚡ Otimizações de Ambiente e Performance

Módulos pesados e desnecessários para a regra de negócio foram desativados no `app/etc/config.php`:

| Grupo de Módulos          | Módulos Desativados                                                           | Motivo da Desativação                                                          |
| :------------------------ | :---------------------------------------------------------------------------- | :----------------------------------------------------------------------------- |
| **Gateways de Pagamento** | `Magento_Paypal*`, `PayPal_Braintree*`, `Magento_PaymentServices*`            | Sem checkout ou cobrança de cartões de crédito.                                |
| **Logística e Fretes**    | `Magento_Fedex`, `Magento_Ups`, `Magento_Dhl`, `Magento_Usps`                 | Sem despacho de mercadorias físicas.                                           |
| **Mídia Comercial**       | `Magento_AdobeStock*`                                                         | Integração com banco de imagens Adobe Stock.                                   |
| **Marketing de Loja**     | `Magento_GoogleAdwords`, `Magento_GoogleAnalytics`, `Magento_GoogleOptimizer` | Rastreamento de conversão de compras.                                          |
| **Segurança 2FA**         | `Magento_TwoFactorAuth`, `Magento_AdminAdobeImsTwoFactorAuth`                 | Desativado para agilizar o desenvolvimento local sem necessidade de SMTP ativo. |

---

## 🖥️ Compatibilidade por Sistema Operacional

### 1. macOS (Apple Silicon M1/M2/M3 ou Intel)
Gerenciado nativamente via Homebrew:
```bash
brew services start mysql
brew services start opensearch
```

### 2. Linux (Ubuntu / Debian / CentOS)
Gerenciado via `systemd`:
```bash
sudo systemctl start mysql
sudo systemctl start opensearch
```

### 3. Windows
- **Recomendado:** Utilizar **WSL 2 (Windows Subsystem for Linux)** com Ubuntu. O Magento 2 não suporta execução direta no CMD/PowerShell devido a links simbólicos e permissões POSIX.

---

## 🚀 Como Executar o Projeto Localmente

### 1. Inicializar os Serviços
Certifique-se de que o **MySQL** (porta `3306`) e o **OpenSearch** (porta `9200`) estejam rodando.

### 2. Subir o Servidor Web do Magento
Na pasta `magento_sou_energy`, execute:
```bash
php -S 127.0.0.1:8000 -t pub/ phpserver/router.php
```

### 3. Acessar a Aplicação
- **Loja / Área do Usuário:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
- **Painel Administrativo:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

---

## 🛠️ Comandos Úteis de Manutenção

```bash
# Limpeza geral de caches do Magento
php bin/magento cache:flush

# Atualização de esquemas de banco e patches de dados
php -d memory_limit=2G bin/magento setup:upgrade --keep-generated

# Reindexação manual completa
php -d memory_limit=2G bin/magento indexer:reindex

# Reindexação específica do grid de clientes
php -d memory_limit=2G bin/magento indexer:reindex customer_grid

# Limpeza de classes e interceptors compilados
rm -rf generated/code/SouEnergy
```

---

## 🌐 APIs REST para o Frontend Vue.js (Fase 4)

O backend expõe as seguintes rotas REST para integração com a SPA desacoplada em Vue.js:

| Endpoint | Método | Autenticação | Descrição |
| :--- | :---: | :---: | :--- |
| `/rest/V1/integration/customer/token` | `POST` | Pública | Login do usuário comum e emissão do token Bearer |
| `/rest/V1/customers/me` | `GET` | Bearer Token | Dados do perfil do usuário e atributos customizados (`terms_version`, `terms_accepted_at`) |
| `/rest/V1/souenergy/notifications` | `GET` | Bearer Token | Lista de notificações do usuário autenticado |
| `/rest/V1/souenergy/notifications/unread-count` | `GET` | Bearer Token | Quantidade de notificações não lidas para o sino 🔔 |
| `/rest/V1/souenergy/notifications/:id/read` | `POST` | Bearer Token | Marca uma notificação como lida |
