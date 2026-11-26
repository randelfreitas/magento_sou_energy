# Magento Sou Energy - Módulo de Autenticação, Notificações e Termos de Uso

Projeto desenvolvido em **Magento 2 Open Source (2.4.7-p3)** com foco na implementação de um sistema modular e desacoplado de autenticação, área restrita de usuário comum, central de notificações e gestão de termos de uso (conformidade LGPD), além de interface administrativa exclusiva e preparação completa para consumo por frontend externo em **Vue.js**.

---

## 🛠️ Stack Tecnológica e Versões

Ambiente homologado e testado:

| Software                | Versão Utilizada         | Função no Projeto                                                                         |
| :---------------------- | :----------------------- | :---------------------------------------------------------------------------------------- |
| **PHP**                 | `8.3.x`                  | Backend runtime (extensões: `intl`, `pdo_mysql`, `bcmath`, `soap`, `sodium`, `gd`, `zip`) |
| **Composer**            | `2.x` (ex: `2.11.x`)     | Gerenciador de dependências PHP                                                           |
| **MySQL**               | `8.0+ / 8.4+ / 26.x`     | Banco de dados relacional principal                                                       |
| **OpenSearch**          | `2.x`                    | Mecanismo de busca e indexação obrigatório do Magento 2.4+                                |
| **Magento Open Source** | `2.4.7-p3`               | Plataforma backend, modularidade EAV e exposição de APIs                                  |
| **Node.js**             | `v20.x` ou `v23.x`       | Ambiente de execução para o frontend desacoplado                                          |
| **NPM**                 | `10.x`                   | Gerenciador de pacotes do ecossistema Node                                                |
| **Vue.js**              | `3.x`                    | Frontend SPA desacoplado (Fase 4)                                                         |

---

## 🚀 Como Executar o Projeto Localmente (Guia Passo a Passo do Zero)

Este guia foi elaborado para permitir que **qualquer pessoa (inclusive iniciantes)** consiga configurar e rodar o projeto em sua máquina local a partir do zero.

---

### Passo 1: Preparação do Sistema Operacional

#### macOS (Apple Silicon M1/M2/M3 ou Intel)
Recomenda-se utilizar o gerenciador de pacotes **Homebrew**. Caso ainda não o tenha instalado, abra o Terminal e execute:
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

#### Windows
O Magento 2 **não roda nativamente no Prompt de Comando ou PowerShell do Windows** devido a requisitos de symlinks (links simbólicos) e permissões de arquivos no padrão POSIX.  
Por isso, é mandatório utilizar o **WSL 2 (Windows Subsystem for Linux)**:
1. Abra o PowerShell como Administrador e execute:
   ```powershell
   wsl --install -d Ubuntu
   ```
2. Reinicie o computador se solicitado e abra o terminal do **Ubuntu** para executar todos os comandos dos próximos passos.

#### Linux (Ubuntu / Debian)
Atualize os repositórios do sistema:
```bash
sudo apt update && sudo apt upgrade -y
```

---

### Passo 2: Instalação das Dependências de Sistema

#### 1. PHP 8.3 e Extensões Necessárias
O Magento 2.4.7 requer PHP 8.3 com extensões específicas de criptografia, manipulação de imagens, XML e banco de dados.

- **No macOS (via Homebrew):**
  ```bash
  brew install php@8.3
  brew link --force --overwrite php@8.3
  ```
  Verifique a versão:
  ```bash
  php -v
  # Deve retornar: PHP 8.3.x
  ```

- **No Ubuntu / Linux / WSL 2:**
  ```bash
  sudo apt install -y software-properties-common
  sudo add-apt-repository ppa:ondrej/php -y
  sudo apt update
  sudo apt install -y php8.3 php8.3-cli php8.3-common php8.3-mysql php8.3-intl php8.3-bcmath \
                      php8.3-soap php8.3-xml php8.3-zip php8.3-gd php8.3-curl php8.3-mbstring php8.3-sodium
  ```

- **Ajuste de Memória do PHP (`php.ini`):**  
  O Magento necessita de bastante memória para compilação e reindexação. Localize seu `php.ini` (com `php --ini`) e garanta os seguintes valores:
  ```ini
  memory_limit = 2G
  max_execution_time = 1800
  zlib.output_compression = On
  ```

#### 2. Composer 2 (Gerenciador de Pacotes PHP)
- **No macOS:**
  ```bash
  brew install composer
  ```
- **No Linux / WSL 2:**
  ```bash
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
  ```
  Verifique com:
  ```bash
  composer --version
  # Deve retornar: Composer version 2.x
  ```

#### 3. Node.js e NPM
Necessário para a suíte de scripts e o frontend desacoplado em Vue.js:
- **No macOS:**
  ```bash
  brew install node
  ```
- **No Linux / WSL 2:**
  ```bash
  curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
  sudo apt install -y nodejs
  ```
  Verifique com:
  ```bash
  node -v
  npm -v
  ```

#### 4. Banco de Dados MySQL 8.0 & Interface Gráfica
O Magento utiliza banco relacional MySQL.

- **Instalação do MySQL:**
  - **No macOS:**
    ```bash
    brew install mysql
    brew services start mysql
    ```
  - **No Linux / WSL 2:**
    ```bash
    sudo apt install -y mysql-server
    sudo systemctl start mysql
    ```

- **Criação do Banco de Dados:**  
  Acesse o MySQL pelo terminal:
  ```bash
  mysql -u root
  ```
  *(Se você tiver configurado senha para o root, use `mysql -u root -p`).*  
  Em seguida, crie o banco de dados do projeto:
  ```sql
  CREATE DATABASE magento_sou_energy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  EXIT;
  ```

- **Interface Gráfica (GUI) Recomendada:**  
  Para visualizar e gerenciar as tabelas com facilidade, instale uma interface visual gratuita:
  - **Sequel Ace** (macOS, gratuito na Mac App Store ou via `brew install --cask sequel-ace`) — *Excelente para Mac!*
  - **DBeaver Community** (Windows, macOS e Linux, gratuito em [dbeaver.io](https://dbeaver.io/))
  - **TablePlus** (Windows, macOS e Linux em [tableplus.com](https://tableplus.com/))
  
  **Configuração da Conexão na GUI:**
  - **Host:** `127.0.0.1`
  - **Porta:** `3306`
  - **Usuário:** `root`
  - **Senha:** *(deixe em branco caso não tenha definido senha, ou digite a sua)*
  - **Database:** `magento_sou_energy`

#### 5. OpenSearch 2.x (Mecanismo de Busca Obrigatório)
A partir do Magento 2.4+, o sistema exige um motor de busca externo (OpenSearch ou Elasticsearch) para funcionar e indexar produtos/clientes.

- **Instalação do OpenSearch:**
  - **No macOS:**
    ```bash
    brew install opensearch
    brew services start opensearch
    ```
  - **No Linux / WSL 2 (Instalação via repositório ou arquivo tarball):**
    Consulte o guia oficial ou instale via script da comunidade.
- **Teste de Verificação do OpenSearch:**  
  Execute no terminal:
  ```bash
  curl http://127.0.0.1:9200/
  ```
  Você deve receber um JSON informando `"tagline": "The OpenSearch Project: https://opensearch.org/"`.

---

### Passo 3: Criação de Usuário e Chaves na Adobe (Marketplace Keys)

Para baixar os pacotes oficiais do Magento via Composer, a Adobe exige autenticação com chaves gratuitas de desenvolvedor.

1. Acesse o site do [Adobe Commerce Marketplace](https://marketplace.magento.com/) e crie uma conta gratuita (Adobe ID) ou faça login caso já possua.
2. No canto superior direito, clique no seu nome de usuário e selecione **My Profile**.
3. Na seção lateral ou menu **Marketplace**, clique em **Access Keys** (sob a aba *Developers*).
4. Na aba **Magento 2**, clique no botão **Create a New Access Key**.
5. Dê um nome de identificação (ex: `Magento Local`) e confirme.
6. O sistema gerará duas chaves:
   - **Public Key:** Esta chave funciona como o seu **Username**.
   - **Private Key:** Esta chave funciona como o seu **Password**.

> [!TIP]
> Você pode salvar essas chaves no arquivo global `~/.composer/auth.json` para não precisar digitá-las repetidamente:
> ```json
> {
>     "http-basic": {
>         "repo.magento.com": {
>             "username": "SUA_PUBLIC_KEY_AQUI",
>             "password": "SUA_PRIVATE_KEY_AQUI"
>         }
>     }
> }
> ```

---

### Passo 4: Instalação e Configuração do Magento

1. **Acessar a Pasta do Projeto:**
   ```bash
   cd magento_sou_energy
   ```

2. **Instalar as Dependências do Composer:**
   ```bash
   composer install
   ```
   *(Caso não tenha criado o `auth.json`, o Composer solicitará o Username [sua Public Key] e o Password [sua Private Key] gerados na Adobe).*

3. **Executar o Instalador Automático do Magento:**
   Execute o comando abaixo para configurar o banco de dados e criar o usuário administrador:
   ```bash
   php -d memory_limit=2G bin/magento setup:install \
     --base-url="http://127.0.0.1:8000/" \
     --db-host="127.0.0.1" \
     --db-name="magento_sou_energy" \
     --db-user="root" \
     --db-password="" \
     --admin-firstname="Admin" \
     --admin-lastname="SouEnergy" \
     --admin-email="admin@souenergy.com.br" \
     --admin-user="admin" \
     --admin-password="AdminPassword123*" \
     --language="pt_BR" \
     --currency="BRL" \
     --timezone="America/Fortaleza" \
     --use-rewrites="1" \
     --search-engine="opensearch" \
     --opensearch-host="127.0.0.1" \
     --opensearch-port="9200" \
     --opensearch-index-prefix="magento2"
   ```

4. **Desativar a Autenticação de Dois Fatores (2FA) para Ambiente Local:**  
   Em ambiente de desenvolvimento local, desativar o 2FA evita a necessidade de configurar um servidor SMTP para envio de e-mails de validação:
   ```bash
   php bin/magento module:disable Magento_TwoFactorAuth Magento_AdminAdobeImsTwoFactorAuth
   ```

5. **Habilitar o Módulo SouEnergy e Atualizar o Banco:**
   ```bash
   php bin/magento module:enable SouEnergy_Notification
   php -d memory_limit=2G bin/magento setup:upgrade --keep-generated
   php -d memory_limit=2G bin/magento setup:di:compile
   php bin/magento cache:flush
   ```

---

### Passo 5: Inicialização do Servidor e Acesso à Aplicação

1. **Certifique-se de que os serviços de apoio estão rodando:**
   - No macOS:
     ```bash
     brew services start mysql
     brew services start opensearch
     ```
   - No Linux / WSL 2:
     ```bash
     sudo systemctl start mysql
     sudo systemctl start opensearch
     ```

2. **Inicie o Servidor Embutido do Magento:**
   Dentro da pasta `magento_sou_energy`, execute:
   ```bash
   php -S 127.0.0.1:8000 -t pub/ phpserver/router.php
   ```

3. **Pronto! Acesse no seu navegador:**
   - **Loja / Área do Usuário:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
   - **Painel Administrativo:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

---

## 🔗 Acessos e Credenciais Pré-configuradas

### 1. Painel Administrativo
- **URL:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
- **Usuário:** `admin`
- **Senha:** `AdminPassword123*`

### 2. Usuário Comum (Área do Cliente)
- **URL Base:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)  
  *(A rota raiz `/` detecta o status de sessão e redireciona automaticamente para o login ou painel do cliente).*
- **URL de Login Direta:** [http://127.0.0.1:8000/customer/account/login/](http://127.0.0.1:8000/customer/account/login/)
- **E-mail:** `randel@souenergy.com.br`
- **Senha:** `UserPassword123*`
- **Nome:** Marcos Randel  
*(Usuário provisionado e indexado automaticamente pelo módulo).*

---

## 🎛️ Painel Administrativo Exclusivo (Menu Lateral)

O menu administrativo foi reestruturado de forma limpa, direta e padronizada. Todas as opções foram organizadas no primeiro nível da barra lateral esquerda, com títulos uniformes:

```
[Barra Lateral do Admin]
 ├── 👥 Gerenciar Usuários         -> Listagem de clientes e auditoria de versão dos termos
 ├── 🔔 Gerenciar Notificações     -> Listagem, disparo, alternância de status (Ativa/Inativa)
 └── 📄 Gerenciar Termos de Uso    -> Controle da versão vigente em tela cheia e disparo automático
```

---

## 📦 Funcionalidades do Módulo `SouEnergy_Notification`

O módulo está localizado em `app/code/SouEnergy/Notification/` e foi concebido respeitando os padrões de arquitetura limpa do Magento 2:

### 1. Usuário Comum e Experiência Enxuta
- **Redirecionamento Inteligente da Raiz:** Ao acessar `http://127.0.0.1:8000/`, o visitante não-autenticado é direcionado imediatamente para a tela de Login (`/customer/account/login/`). Se já estiver logado, vai direto para sua área restrita (`/customer/account/`).
- **Tela de Login Centralizada e Clean:** O formulário de login foi simplificado em formato de card centralizado, removendo ruídos de e-commerce como o bloco "Novos Clientes" e cabeçalhos genéricos.
- **Identidade Visual SouEnergy:** O logotipo padrão Luma foi substituído pelo logo corporativo estilizado "SouEnergy". Elementos de loja (carrinho de compras, barra de busca de produtos, comparadores e seletor de lojas) foram removidos da interface.
- **Painel do Cliente Minimalista:** A barra lateral interna da conta exibe estritamente:
  - **Dados da Conta** (informações cadastrais essenciais).
  - **🔔 Minhas Notificações** (central de avisos com contador e botão de leitura).
- **Atributos de Auditoria EAV do Cliente:**
  - `terms_version`: Versão do termo aceita pelo usuário (ex: `v1.0`, `v2.0`).
  - `terms_accepted_at`: Data e hora exatas do consentimento (timestamp auditável para fins de LGPD).

### 2. Central de Notificações com Alternância de Status
- **Tabela Dedicada:** Estruturada via `etc/db_schema.xml` (`souenergy_notification`) com índices em `customer_id`, `is_read` e `is_active`.
- **Status Ativa / Inativa (`is_active`):** A tabela conta com uma coluna de status dinâmico (`✓ Ativa` / `✕ Inativa`). O administrador pode desativar ou reativar qualquer notificação com **1 clique** na coluna de Ações.
- **Ações e Redirecionamentos Personalizados:** Cada notificação suporta uma URL de ação (`action_url`) e rótulo de botão (`action_label`), permitindo direcionar o cliente diretamente para telas específicas (ex: página de termos, novidades ou avisos).
- **Sino Interativo no Cabeçalho 🔔:** Integrado ao topo da loja, exibe em tempo real o contador de notificações pendentes ativas.
- **Filtro de Exibição:** Notificações desativadas pelo administrador são imediatamente ocultadas do sino e da listagem do cliente.

### 3. Gestão dos Termos de Uso (Conformidade LGPD)
- **Interface Administrativa em Tela Cheia:** A página **Gerenciar Termos de Uso** adota layout centralizado de largura total (100%), com cards responsivos de métricas (*Versão Vigente*, *Total de Usuários*, *Usuários Atualizados*, *Aceites Pendentes*).
- **Disparo Automático de Notificações:** Ao salvar uma nova versão dos termos no painel, o sistema **dispara instantaneamente uma notificação geral** para toda a base com o link para leitura.
- **Modal Inteligente no Login:** Se o usuário fizer login e sua versão aceita for inferior à versão vigente, um modal em destaque com *backdrop blur* é apresentado.
- **Supressão Inteligente:** Caso o administrador desative a notificação dos termos no menu de notificações, **o modal é automaticamente suprimido**, garantindo controle total ao operador do sistema.
- **Auditoria Visual no Grid de Usuários:** A coluna de termos na listagem de usuários comuns exibe dinamicamente a versão vigente no cabeçalho e classifica os usuários com badges coloridos:
  - `✓ v2.0 (Atualizado)`: Usuário já aceitou a versão vigente.
  - `⚠️ Desatualizado (v1.0)`: Usuário com versão legada.
  - `❌ Não Aceitou`: Usuário pendente de aceite.

---

## ⚡ Otimizações de Performance e Ambiente

Para assegurar inicialização ultra-rápida e consumo mínimo de memória (ideal para desenvolvimento local em máquinas com 8GB a 16GB de RAM), os seguintes módulos não essenciais foram desativados:

| Grupo de Módulos          | Módulos Desativados                                                           | Motivo da Desativação                                                          |
| :------------------------ | :---------------------------------------------------------------------------- | :----------------------------------------------------------------------------- |
| **Gateways de Pagamento** | `Magento_Paypal*`, `PayPal_Braintree*`, `Magento_PaymentServices*`            | O escopo do projeto não envolve checkout nem cobrança de cartão.              |
| **Logística e Fretes**    | `Magento_Fedex`, `Magento_Ups`, `Magento_Dhl`, `Magento_Usps`                 | Sem cálculo ou despacho de fretes físicos.                                     |
| **Mídia Comercial**       | `Magento_AdobeStock*`                                                         | Integração com banco de imagens pago da Adobe.                                 |
| **Marketing de Loja**     | `Magento_GoogleAdwords`, `Magento_GoogleAnalytics`, `Magento_GoogleOptimizer` | Rastreamento analítico comercial.                                              |
| **Segurança 2FA**         | `Magento_TwoFactorAuth`, `Magento_AdminAdobeImsTwoFactorAuth`                 | Permite autenticação ágil no ambiente local sem necessidade de servidor SMTP.  |

---

## 🛠️ Comandos Úteis de Manutenção

Execute estes comandos na pasta `magento_sou_energy`:

```bash
# Limpeza geral de caches do Magento
php bin/magento cache:flush

# Atualização de esquemas de banco e patches de dados
php -d memory_limit=2G bin/magento setup:upgrade --keep-generated

# Compilação de DI e interceptors
php -d memory_limit=2G bin/magento setup:di:compile

# Reindexação manual completa
php -d memory_limit=2G bin/magento indexer:reindex

# Reindexação específica do grid de clientes
php -d memory_limit=2G bin/magento indexer:reindex customer_grid

# Limpeza de classes geradas do módulo SouEnergy
rm -rf generated/code/SouEnergy
```

---

## 🌐 APIs REST para o Frontend Vue.js (Fase 4)

O backend disponibiliza endpoints REST nativos prontos para integração com a SPA desacoplada:

| Endpoint | Método | Autenticação | Descrição |
| :--- | :---: | :---: | :--- |
| `/rest/V1/integration/customer/token` | `POST` | Pública | Login do usuário comum e emissão do token JWT/Bearer |
| `/rest/V1/customers/me` | `GET` | Bearer Token | Retorna o perfil completo e atributos customizados (`terms_version`, `terms_accepted_at`) |
| `/rest/V1/souenergy/notifications` | `GET` | Bearer Token | Retorna a lista de notificações ativas do usuário |
| `/rest/V1/souenergy/notifications/unread-count` | `GET` | Bearer Token | Quantidade de notificações não lidas para o sino 🔔 |
| `/rest/V1/souenergy/notifications/:id/read` | `POST` | Bearer Token | Marca uma notificação individual como lida |
