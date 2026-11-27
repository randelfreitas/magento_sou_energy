# Magento Sou Energy - Módulo de Autenticação, Notificações e Política de Privacidade

Projeto desenvolvido em **Magento 2 Open Source (2.4.7-p3)** com foco na implementação de um sistema modular de autenticação, área restrita de usuário comum, central de notificações e gestão de política de privacidade, com interface administrativa exclusiva e navegação nativa.

---

## Sumário

1. [Passo a passo do zero](#passo-a-passo-do-zero)
2. [Já tenho instalado PHP, MySQL, OpenSearch](#ja-tenho-instalado)
3. [Acessos e credenciais](#acessos-e-credenciais)
4. [Telas de Admin](#telas-de-admin)
5. [Telas de Usuário comum](#telas-de-usuario-comum)
6. [Casos de testes](#casos-de-testes)

---

## Stack Tecnológica e Versões

| Software                | Versão Utilizada     | Função no Projeto                                                                         |
| :---------------------- | :------------------- | :---------------------------------------------------------------------------------------- |
| **PHP**                 | `8.3.x`              | Backend runtime (extensões: `intl`, `pdo_mysql`, `bcmath`, `soap`, `sodium`, `gd`, `zip`) |
| **Composer**            | `2.x` (ex: `2.11.x`) | Gerenciador de dependências PHP                                                           |
| **MySQL**               | `8.0+ / 8.4+ / 26.x` | Banco de dados relacional principal                                                       |
| **OpenSearch**          | `2.x`                | Mecanismo de busca e indexação obrigatório do Magento 2.4+                                |
| **Magento Open Source** | `2.4.7-p3`           | Plataforma web, modularidade EAV e arquitetura MVC nativa                                 |

---

## Como Executar o Projeto Localmente (Guia Passo a Passo) <a id="passo-a-passo-do-zero"></a>

### Passo 1: Preparação do Sistema Operacional

#### macOS (Apple Silicon M1/M2/M3 ou Intel)

Recomenda-se utilizar o gerenciador de pacotes **Homebrew**. Caso ainda não o tenha instalado, abra o Terminal e execute:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

#### Windows

O Magento 2 não roda nativamente no Prompt de Comando ou PowerShell do Windows devido a requisitos de links simbólicos e permissões POSIX.  
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
  O Magento necessita de memória adequada para compilação e reindexação. Localize seu `php.ini` (com `php --ini`) e garanta os seguintes valores:
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

#### 3. Banco de Dados MySQL 8.0 e Interface Gráfica

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

    _(Se você configurou senha para o root, utilize `mysql -u root -p`)._  
    Em seguida, crie a base de dados do projeto:

    ```sql
    CREATE DATABASE magento_sou_energy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    EXIT;
    ```

- **Interface Gráfica (GUI) Recomendada:**  
  Para gerenciar as tabelas e dados visualmente, utilize ferramentas como:
    - **Sequel Ace** (macOS, gratuito na Mac App Store ou via `brew install --cask sequel-ace`).
    - **DBeaver Community** (Windows, macOS e Linux, gratuito em [dbeaver.io](https://dbeaver.io/)).
    - **TablePlus** (Windows, macOS e Linux em [tableplus.com](https://tableplus.com/)).

    **Parâmetros de Conexão:**
    - **Host:** `127.0.0.1`
    - **Porta:** `3306`
    - **Usuário:** `root`
    - **Senha:** _(deixe em branco caso não tenha definido, ou informe a sua senha)_
    - **Database:** `magento_sou_energy`

#### 4. OpenSearch 2.x (Mecanismo de Busca Obrigatório)

A partir do Magento 2.4+, o sistema exige um motor de busca externo (OpenSearch ou Elasticsearch) para indexação e funcionamento do catálogo e cadastros.

- **Instalação do OpenSearch:**
    - **No macOS:**
        ```bash
        brew install opensearch
        brew services start opensearch
        ```
    - **No Linux / WSL 2:**
      Instale o OpenSearch conforme a documentação oficial da sua distribuição Linux.
- **Teste de Verificação do OpenSearch:**  
  Execute no terminal:
    ```bash
    curl http://127.0.0.1:9200/
    ```
    O retorno deve ser um JSON informando `"tagline": "The OpenSearch Project: https://opensearch.org/"`.

---

### Passo 3: Criação de Usuário e Chaves na Adobe (Marketplace Keys) <a id="ja-tenho-instalado"></a>

Para baixar os pacotes oficiais do Magento via Composer, a Adobe exige autenticação com chaves gratuitas de desenvolvedor.

1. Acesse o portal [Adobe Commerce Marketplace](https://marketplace.magento.com/) e faça login ou crie uma conta gratuita (Adobe ID).
2. No canto superior direito, clique no seu nome e acesse **My Profile**.
3. Na seção lateral ou menu **Marketplace**, clique em **Access Keys** (sob a aba _Developers_).
4. Na aba **Magento 2**, clique em **Create a New Access Key**.
5. Informe um nome identificador (ex: `Magento Local`) e confirme.
6. O sistema gerará duas chaves:
    - **Public Key:** Utilizada como o seu **Username**.
    - **Private Key:** Utilizada como a sua **Password**.

Você pode configurar essas credenciais globalmente no arquivo `~/.composer/auth.json`:

```json
{
    "http-basic": {
        "repo.magento.com": {
            "username": "SUA_PUBLIC_KEY_AQUI",
            "password": "SUA_PRIVATE_KEY_AQUI"
        }
    }
}
```

---

### Passo 4: Instalação e Configuração do Magento

1. **Acessar a Pasta do Projeto:**

    ```bash
    cd magento_sou_energy
    ```

2. **Instalar as Dependências via Composer:**

    ```bash
    composer install
    ```

3. **Executar o Instalador Automático do Magento:**
   Execute o comando completo abaixo com todas as flags necessárias para contornar checagens rígidas de versão de MySQL e memória:

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
      --backend-frontname="admin" \
      --language="pt_BR" \
      --currency="BRL" \
      --timezone="America/Fortaleza" \
      --use-rewrites="1" \
      --search-engine="opensearch" \
      --opensearch-host="127.0.0.1" \
      --opensearch-port="9200" \
      --opensearch-index-prefix="magento2" \
      --cleanup-database \
      --skip-db-validation
    ```

    **Observações:**
    - `-d memory_limit=2G`: Eleva o limite de memória do PHP para a instalação, prevenindo estouros de memória na CLI.
    - `--skip-db-validation`: Ignora a checagem rígida de versão do MySQL do Magento, permitindo a execução em MySQL 8.4 LTS ou versões recentes sem bloqueios.
    - `--cleanup-database`: Limpa tabelas residuais de tentativas anteriores interrompidas, evitando erros de duplicidade.
    - `--backend-frontname="admin"`: Fixa a rota do painel administrativo em `/admin`.

    > **Possível erro que pode surgir: Erro de Versão do MySQL (RDBMS not supported)**  
    > Caso a instalação falhe com o erro:
    > `Current version of RDBMS is not supported. Used Version: 26.10.0. Supported versions: MySQL-8, MySQL-5.7, MariaDB-(10.2-10.6)`
    >
    > Esse comportamento ocorre porque certas distribuições do MySQL (como a versão 26.x instalada pelo Homebrew no macOS) utilizam uma numeração de compilação que o validador interno do Magento barra por padrão. Para solucionar:
    >
    > 1. Abra o arquivo `app/etc/di.xml`.
    > 2. Localize a tag `SqlVersionProvider` (por volta da linha 1857):
    >     ```xml
    >     <type name="Magento\Framework\DB\Adapter\SqlVersionProvider">
    >         <arguments>
    >             <argument name="supportedVersionPatterns" xsi:type="array">
    >                 <item name="MySQL-8" xsi:type="string">^8\.0\.|^26\.</item>
    >     ```
    > 3. Adicione `|^26\.` no item `MySQL-8`, conforme exibido acima, para autorizar a versão 26.x.
    > 4. Remova o arquivo de configuração gerado pela metade:
    >     ```bash
    >     rm -f app/etc/env.php
    >     ```
    > 5. Execute novamente o comando `setup:install`.

4. **Desativar a Autenticação de Dois Fatores (2FA) para Desenvolvimento Local:**

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

1. **Verificar os serviços de apoio:**
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

2. **Iniciar o Servidor Embutido do Magento:**
   Dentro da pasta `magento_sou_energy`, execute:

    ```bash
    php -S 127.0.0.1:8000 -t pub/ phpserver/router.php
    ```

3. **Acessar a Aplicação:**
    - **Área do Usuário Comum:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
    - **Painel Administrativo:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

---

## Acessos e Credenciais Pré-configuradas <a id="acessos-e-credenciais"></a>

### 1. Painel Administrativo

- **URL:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
- **Usuário:** `admin`
- **Senha:** `AdminPassword123*`

### 2. Usuário Comum (Área do Cliente)

- **URL Base:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/) _(redireciona automaticamente para login ou painel)_
- **URL Direta de Login:** [http://127.0.0.1:8000/customer/account/login/](http://127.0.0.1:8000/customer/account/login/)
- **E-mail:** `randel@souenergy.com.br`
- **Senha:** `UserPassword123*`
- **Nome:** Marcos Randel _(criado automaticamente via Data Patch)_

---

## Visão Geral das Telas e Arquitetura do Sistema

### 1. Painel Administrativo <a id="telas-de-admin"></a>

O menu lateral foi remodelado com três entradas diretas de primeiro nível:

- **Gerenciar Usuários (`/admin/souenergy_notification/customer/index`):**
    - Grid de clientes com colunas dedicadas para auditoria da política de privacidade.
    - O cabeçalho da coluna de política de privacidade indica a versão vigente do sistema (ex: `Política de Privacidade (Vigente: v2.0)`).
    - Cada registro exibe badges de conformidade:
        - `✓ v2.0 (Atualizado)`: Usuário já aceitou a versão vigente.
        - `⚠️ Desatualizado (v1.0)`: Usuário com versão anterior.
        - `❌ Não Aceitou`: Usuário com aceite pendente.
    - Exibe data e hora exatas em que o aceite foi registrado pelo cliente.

- **Gerenciar Notificações (`/admin/souenergy_notification/notification/index`):**
    - Grid com ID, Título, Mensagem, Destinatário (Geral ou Cliente Específico), Link de Ação e Status (`✓ Ativa` / `✕ Inativa`).
    - Coluna de Ações com alternância de status em 1 clique (Ativar / Desativar) e botão de exclusão.
    - Desativar uma notificação reflete instantaneamente no storefront, ocultando-a do sino e da listagem do cliente.

- **Criar Notificação (`/admin/souenergy_notification/notification/new`):**
    - Formulário administrativo com seleção de tipo de destinatário (Todos os clientes ou cliente específico via autocomplete/ID).
    - Campos opcionais para URL de ação (`action_url`) e rótulo do botão (`action_label`), permitindo encaminhar o cliente diretamente para qualquer rota.

- **Gerenciar Política de Privacidade (`/admin/souenergy_notification/terms/manage`):**
    - Interface em tela cheia centralizada com quatro cards de métricas consolidadas:
        - _Versão Vigente_: Identificação da versão ativa no momento.
        - _Total de Clientes_: Número total de usuários cadastrados na base.
        - _Clientes com a Versão Vigente_: Total de usuários com aceite confirmado na versão atual.
        - _Aceites Pendentes_: Quantidade de clientes que necessitam atualizar o consentimento.
    - Formulário de publicação de nova versão (campo de versão e campo de texto legal).
    - Botão de publicação que grava a nova versão e dispara automaticamente uma notificação geral para todos os usuários.

---

### 2. Área do Usuário Comum (Storefront) <a id="telas-de-usuario-comum"></a>

- **Redirecionamento Automático da Raiz (`/`):**
    - Visitantes não autenticados que acessam `http://127.0.0.1:8000/` são redirecionados para a tela de Login.
    - Usuários já autenticados são direcionados diretamente para o painel Minha Conta.

- **Tela de Login (`/customer/account/login/`):**
    - Card centralizado minimalista com identidade corporativa SouEnergy.
    - Remoção de elementos desnecessários de loja (como vitrines, bloco de novos clientes e botões secundários).

- **Cabeçalho da Loja:**
    - Logotipo oficial SouEnergy no topo esquerdo.
    - Sino de notificações com contador numérico de mensagens pendentes ativas, atualizado em tempo real.
    - Links limpos para acesso à conta e encerramento de sessão.

- **Painel Minha Conta (`/customer/account/`):**
    - Interface enxuta focada em dados essenciais de cadastro.
    - Menu lateral simplificado contendo apenas:
        - _Dados da Conta_
        - _Minhas Notificações_

- **Central de Notificações do Cliente (`/souenergy_notification/customer/index/`):**
    - Listagem completa das notificações destinadas ao usuário ou de caráter geral.
    - Botão para marcar mensagem como lida e botão de ação direta direcionando para a página informada no cadastro da notificação.

- **Página de Política de Privacidade (`/souenergy/terms/index/`):**
    - Exibição integral do texto da versão vigente da política de privacidade.
    - Botão de confirmação formal ("Li e Aceito a Versão ..."), que grava o aceite e o timestamp auditável no perfil do usuário.

- **Modal de Política de Privacidade:**
    - Janela modal com efeito de desfoque de fundo que aparece automaticamente no momento do login quando a versão aceita pelo usuário é inferior à versão vigente.
    - Permite direcionar o cliente imediatamente para a leitura e aceite, funcionando de forma independente do status (ativo/inativo) das notificações.

---

## Roteiro de Testes e Validação Funcional (Casos de Uso) <a id="casos-de-testes"></a>

### Caso de Teste 1: Redirecionamento da Raiz e Login do Usuário

1. Abra o navegador e acesse a URL raiz: [http://127.0.0.1:8000/](http://127.0.0.1:8000/).
2. **Resultado esperado:** O sistema identifica que não há sessão ativa e redireciona automaticamente para [http://127.0.0.1:8000/customer/account/login/](http://127.0.0.1:8000/customer/account/login/).
3. Informe as credenciais do cliente:
    - E-mail: `randel@souenergy.com.br`
    - Senha: `UserPassword123*`
4. Clique em **Entrar**.
5. **Resultado esperado:** O usuário é autenticado e direcionado para a tela Minha Conta. O cabeçalho exibe o logotipo SouEnergy, o sino de notificações e surge automaticamente em destaque na tela o modal de aceite da política de privacidade (caso o usuário ainda não tenha registrado o aceite da versão vigente).

---

### Caso de Teste 2: Auditoria de Usuários no Painel Administrativo

1. Em outra janela do navegador (ou guia anônima), acesse o Admin: [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin).
2. Autentique-se com:
    - Usuário: `admin`
    - Senha: `AdminPassword123*`
3. No menu lateral, clique em **Gerenciar Usuários**.
4. Localize o registro de Marcos Randel na listagem.
5. **Resultado esperado:** A coluna de política de privacidade exibe a versão atual cadastrada e a coluna de data/hora registra o momento do aceite.

---

### Caso de Teste 3: Publicação de Nova Versão da Política de Privacidade e Disparo Automático

1. No menu lateral do Admin, clique em **Gerenciar Política de Privacidade**.
2. Observe os cards de métricas no topo (versão atual, total de clientes e aceites).
3. No formulário abaixo dos cards, informe uma nova versão (ex: altere de `v1.0` para `v2.0`).
4. Atualize o texto no campo de conteúdo com novas cláusulas e clique em **Salvar Nova Versão e Notificar Usuários**.
5. **Resultado esperado:** A página recarrega com mensagem de sucesso confirmando a atualização e os cards passam a exibir a nova versão vigente com contadores recalculados.
6. Clique no menu lateral em **Gerenciar Notificações**.
7. **Resultado esperado:** Uma nova notificação geral foi criada de forma automática pelo sistema, apontando para a URL da política de privacidade e com status `✓ Ativa`.

---

### Caso de Teste 4: Exibição do Modal no Storefront e Confirmação de Aceite

1. Retorne à aba do usuário comum (`randel@souenergy.com.br`) e atualize a página (ou realize logout e login novamente).
2. **Resultado esperado:**
    - O **Modal de Atualização da Política de Privacidade** é aberto em primeiro plano sobre a tela, informando que a política de privacidade foi atualizada.
    - O sino no cabeçalho exibe o badge numérico incrementado com a nova notificação.
3. No modal, clique no botão **Ler e Aceitar Política de Privacidade ->**.
4. O sistema abrirá a página [http://127.0.0.1:8000/souenergy/terms/index/](http://127.0.0.1:8000/souenergy/terms/index/) com o texto completo da versão vigente.
5. Clique no botão **Li e Aceito a Versão ...**.
6. **Resultado esperado:** O aceite é processado, o usuário é redirecionado para Minha Conta com mensagem de sucesso e o modal **não é mais exibido** ao navegar ou atualizar a página.

---

### Caso de Teste 5: Verificação de Auditoria no Admin Após Aceite

1. Retorne à aba do Admin e clique novamente em **Gerenciar Usuários**.
2. **Resultado esperado:** O badge do cliente Marcos Randel foi atualizado para `✓ v2.0 (Atualizado)` e o campo de data/hora reflete o timestamp do novo consentimento.
3. Acesse **Gerenciar Política de Privacidade**.
4. **Resultado esperado:** O card _Clientes com a Versão Vigente_ foi incrementado e o card de _Aceites Pendentes_ foi reduzido.

---

### Caso de Teste 6: Ativação e Desativação de Notificação (Independente do Modal)

1. No Admin, acesse **Gerenciar Política de Privacidade** e publique uma versão experimental (ex: `v3.0`).
2. Acesse o menu **Gerenciar Notificações**.
3. Localize a notificação correspondente à versão `v3.0` e, na coluna de Ações, clique em **Desativar**.
4. **Resultado esperado:** O status da linha muda imediatamente para `✕ Inativa`.
5. Volte à aba do usuário comum e recarregue a página.
6. **Resultado esperado:**
    - A notificação inativa deixa de aparecer no sino e em **Minhas Notificações**.
    - O modal de aceite da política de privacidade **continua sendo exibido** normalmente, pois o cliente ainda não aceitou a versão vigente `v3.0`.
7. Retorne ao Admin em **Gerenciar Notificações** e clique em **Ativar**.
8. Ao recarregar a tela do cliente, a notificação volta a aparecer no sino e em **Minhas Notificações**.

---

### Caso de Teste 7: Notificação Manual e Marcação de Leitura

1. No Admin, acesse **Gerenciar Notificações** e clique no botão **Nova Notificação**.
2. Preencha os campos:
    - Título: `Aviso de Manutenção Programada`
    - Mensagem: `Nossos serviços passarão por melhorias no próximo fim de semana.`
    - Destinatário: Selecione _Geral (Todos os Clientes)_ ou escolha o cliente específico.
    - Link de Ação (URL): Opcional (ex: `http://127.0.0.1:8000/`).
    - Rótulo do Botão: Opcional (ex: `Saiba Mais`).
3. Clique em **Salvar Notificação**.
4. Acesse a tela do usuário comum: o sino de notificações exibirá o novo contador numérico.
5. Clique no sino para visualizar ou acesse o menu **Minhas Notificações**.
6. Clique no botão **Marcar como lida**.
7. **Resultado esperado:** A notificação é marcada como lida e o contador numérico do sino é reduzido instantaneamente.

---

## Otimizações de Performance e Ambiente

Para assegurar inicialização rápida e consumo mínimo de memória em desenvolvimento local, módulos de e-commerce não utilizados pelo escopo foram desativados:

| Grupo de Módulos          | Módulos Desativados                                                           | Motivo da Desativação                                                         |
| :------------------------ | :---------------------------------------------------------------------------- | :---------------------------------------------------------------------------- |
| **Gateways de Pagamento** | `Magento_Paypal*`, `PayPal_Braintree*`, `Magento_PaymentServices*`            | O escopo do projeto não envolve checkout nem cobrança de cartão.              |
| **Logística e Fretes**    | `Magento_Fedex`, `Magento_Ups`, `Magento_Dhl`, `Magento_Usps`                 | Sem cálculo ou despacho de mercadorias físicas.                               |
| **Mídia Comercial**       | `Magento_AdobeStock*`                                                         | Integração com banco de imagens pago da Adobe.                                |
| **Marketing de Loja**     | `Magento_GoogleAdwords`, `Magento_GoogleAnalytics`, `Magento_GoogleOptimizer` | Rastreamento analítico comercial.                                             |
| **Segurança 2FA**         | `Magento_TwoFactorAuth`, `Magento_AdminAdobeImsTwoFactorAuth`                 | Permite autenticação ágil no ambiente local sem necessidade de servidor SMTP. |

---

## Comandos Úteis de Manutenção

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
