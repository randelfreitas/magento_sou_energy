# Magento Sou Energy - Modulo de Autenticacao, Notificacoes e Termos de Uso

Projeto desenvolvido em **Magento 2 Open Source (2.4.7-p3)** com foco na implementacao de um sistema modular e desacoplado de autenticacao, area restrita de usuario comum, central de notificacoes e gestao de termos de uso (conformidade LGPD), alem de interface administrativa exclusiva e preparacao completa para consumo por frontend externo desacoplado via APIs REST.

---

## Stack Tecnologica e Versoes

Ambiente homologado e testado:

| Software                | Versao Utilizada     | Funcao no Projeto                                                                         |
| :---------------------- | :------------------- | :---------------------------------------------------------------------------------------- |
| **PHP**                 | `8.3.x`              | Backend runtime (extensoes: `intl`, `pdo_mysql`, `bcmath`, `soap`, `sodium`, `gd`, `zip`) |
| **Composer**            | `2.x` (ex: `2.11.x`) | Gerenciador de dependencias PHP                                                           |
| **MySQL**               | `8.0+ / 8.4+ / 26.x` | Banco de dados relacional principal                                                       |
| **OpenSearch**          | `2.x`                | Mecanismo de busca e indexacao obrigatorio do Magento 2.4+                                |
| **Magento Open Source** | `2.4.7-p3`           | Plataforma backend, modularidade EAV e exposicao de APIs REST                             |

---

## Como Executar o Projeto Localmente (Guia Passo a Passo do Zero)

---

### Passo 1: Preparacao do Sistema Operacional

#### macOS (Apple Silicon M1/M2/M3 ou Intel)

Recomenda-se utilizar o gerenciador de pacotes **Homebrew**. Caso ainda nao o tenha instalado, abra o Terminal e execute:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

#### Windows

O Magento 2 nao roda nativamente no Prompt de Comando ou PowerShell do Windows devido a requisitos de links simbolicos e permissoes POSIX.  
Por isso, e mandatorio utilizar o **WSL 2 (Windows Subsystem for Linux)**:

1. Abra o PowerShell como Administrador e execute:
    ```powershell
    wsl --install -d Ubuntu
    ```
2. Reinicie o computador se solicitado e abra o terminal do **Ubuntu** para executar todos os comandos dos proximos passos.

#### Linux (Ubuntu / Debian)

Atualize os repositorios do sistema:

```bash
sudo apt update && sudo apt upgrade -y
```

---

### Passo 2: Instalacao das Dependencias de Sistema

#### 1. PHP 8.3 e Extensoes Necessarias

O Magento 2.4.7 requer PHP 8.3 com extensoes especificas de criptografia, manipulacao de imagens, XML e banco de dados.

- **No macOS (via Homebrew):**

    ```bash
    brew install php@8.3
    brew link --force --overwrite php@8.3
    ```

    Verifique a versao:

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

- **Ajuste de Memoria do PHP (`php.ini`):**  
  O Magento necessita de memoria adequada para compilacao e reindexacao. Localize seu `php.ini` (com `php --ini`) e garanta os seguintes valores:
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

#### 3. Banco de Dados MySQL 8.0 e Interface Grafica

O Magento utiliza banco relacional MySQL.

- **Instalacao do MySQL:**
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

- **Criacao do Banco de Dados:**  
  Acesse o MySQL pelo terminal:

    ```bash
    mysql -u root
    ```

    _(Se voce configurou senha para o root, utilize `mysql -u root -p`)._  
    Em seguida, crie a base de dados do projeto:

    ```sql
    CREATE DATABASE magento_sou_energy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    EXIT;
    ```

- **Interface Grafica (GUI) Recomendada:**  
  Para gerenciar as tabelas e dados visualmente, utilize ferramentas como:
    - **Sequel Ace** (macOS, gratuito na Mac App Store ou via `brew install --cask sequel-ace`).
    - **DBeaver Community** (Windows, macOS e Linux, gratuito em [dbeaver.io](https://dbeaver.io/)).
    - **TablePlus** (Windows, macOS e Linux em [tableplus.com](https://tableplus.com/)).

    **Parametros de Conexao:**
    - **Host:** `127.0.0.1`
    - **Porta:** `3306`
    - **Usuario:** `root`
    - **Senha:** _(deixe em branco caso nao tenha definido, ou informe a sua senha)_
    - **Database:** `magento_sou_energy`

#### 4. OpenSearch 2.x (Mecanismo de Busca Obrigatorio)

A partir do Magento 2.4+, o sistema exige um motor de busca externo (OpenSearch ou Elasticsearch) para indexacao e funcionamento do catalogo e cadastros.

- **Instalacao do OpenSearch:**
    - **No macOS:**
        ```bash
        brew install opensearch
        brew services start opensearch
        ```
    - **No Linux / WSL 2:**
      Instale o OpenSearch conforme a documentacao oficial da sua distribuicao Linux.
- **Teste de Verificacao do OpenSearch:**  
  Execute no terminal:
    ```bash
    curl http://127.0.0.1:9200/
    ```
    O retorno deve ser um JSON informando `"tagline": "The OpenSearch Project: https://opensearch.org/"`.

---

### Passo 3: Criacao de Usuario e Chaves na Adobe (Marketplace Keys)

Para baixar os pacotes oficiais do Magento via Composer, a Adobe exige autenticacao com chaves gratuitas de desenvolvedor.

1. Acesse o portal [Adobe Commerce Marketplace](https://marketplace.magento.com/) e faca login ou crie uma conta gratuita (Adobe ID).
2. No canto superior direito, clique no seu nome e acesse **My Profile**.
3. Na secao lateral ou menu **Marketplace**, clique em **Access Keys** (sob a aba _Developers_).
4. Na aba **Magento 2**, clique em **Create a New Access Key**.
5. Informe um nome identificador (ex: `Magento Local`) e confirme.
6. O sistema gerara duas chaves:
    - **Public Key:** Utilizada como o seu **Username**.
    - **Private Key:** Utilizada como a sua **Password**.

Voce pode configurar essas credenciais globalmente no arquivo `~/.composer/auth.json`:

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

### Passo 4: Instalacao e Configuracao do Magento

1. **Acessar a Pasta do Projeto:**

    ```bash
    cd magento_sou_energy
    ```

2. **Instalar as Dependencias via Composer:**

    ```bash
    composer install
    ```

3. **Executar o Instalador Automatico do Magento:**
   Execute o comando completo abaixo com todas as flags necessarias para contornar checagens rigidas de versao de MySQL e memoria:

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

    **Notas Tecnicas dos Parametros:**
    - `-d memory_limit=2G`: Eleva o limite de memoria do PHP para a instalacao, prevenindo estouros de memoria na CLI.
    - `--skip-db-validation`: Ignora a checagem rigida de versao do MySQL do Magento, permitindo a execucao em MySQL 8.4 LTS ou versoes recentes sem bloqueios.
    - `--cleanup-database`: Limpa tabelas residuais de tentativas anteriores interrompidas, evitando erros de duplicidade.
    - `--backend-frontname="admin"`: Fixa a rota do painel administrativo em `/admin`.

4. **Desativar a Autenticacao de Dois Fatores (2FA) para Desenvolvimento Local:**

    ```bash
    php bin/magento module:disable Magento_TwoFactorAuth Magento_AdminAdobeImsTwoFactorAuth
    ```

5. **Habilitar o Modulo SouEnergy e Atualizar o Banco:**
    ```bash
    php bin/magento module:enable SouEnergy_Notification
    php -d memory_limit=2G bin/magento setup:upgrade --keep-generated
    php -d memory_limit=2G bin/magento setup:di:compile
    php bin/magento cache:flush
    ```

---

### Passo 5: Inicializacao do Servidor e Acesso a Aplicacao

1. **Verificar os servicos de apoio:**
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

3. **Acessar a Aplicacao:**
    - **Area do Usuario Comum:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
    - **Painel Administrativo:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

---

## Acessos e Credenciais Pre-configuradas

### 1. Painel Administrativo

- **URL:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
- **Usuario:** `admin`
- **Senha:** `AdminPassword123*`

### 2. Usuario Comum (Area do Cliente)

- **URL Base:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/) _(redireciona automaticamente para login ou painel)_
- **URL Direta de Login:** [http://127.0.0.1:8000/customer/account/login/](http://127.0.0.1:8000/customer/account/login/)
- **E-mail:** `randel@souenergy.com.br`
- **Senha:** `UserPassword123*`
- **Nome:** Marcos Randel _(criado automaticamente via Data Patch)_

---

## Visao Geral das Telas e Arquitetura do Sistema

O sistema foi estruturado para atender aos requisitos de conformidade legal (LGPD), comunicacao com o cliente e gestao administrativa simplificada.

### 1. Painel Administrativo

O menu lateral foi remodelado com tres entradas diretas de primeiro nivel:

- **Gerenciar Usuarios (`/admin/souenergy_notification/customer/index`):**
    - Grid de clientes com colunas dedicadas para auditoria de termos.
    - O cabecalho da coluna de termos indica a versao vigente do sistema (ex: `Termos de Uso (Vigente: v2.0)`).
    - Cada registro exibe badges de conformidade:
        - `✓ v2.0 (Atualizado)`: Usuario já aceitou a versão vigente.
        - `⚠️ Desatualizado (v1.0)`: Usuario com versão anterior.
        - `❌ Não Aceitou`: Usuario com aceite pendente.
    - Exibe data e hora exatas em que o aceite foi registrado pelo cliente.

- **Gerenciar Notificacoes (`/admin/souenergy_notification/notification/index`):**
    - Grid com ID, Titulo, Mensagem, Destinatario (Geral ou Cliente Especifico), Link de Acao e Status (`✓ Ativa` / `✕ Inativa`).
    - Coluna de Acoes com alternancia de status em 1 clique (Ativar / Desativar) e botao de exclusao.
    - Desativar uma notificacao reflete instantaneamente no storefront, ocultando-a do sino e da listagem do cliente.

- **Criar Notificacao (`/admin/souenergy_notification/notification/new`):**
    - Formulario administrativo com selecao de tipo de destinatario (Todos os clientes ou cliente especifico via autocomplete/ID).
    - Campos opcionais para URL de acao (`action_url`) e rotulo do botao (`action_label`), permitindo encaminhar o cliente diretamente para qualquer rota.

- **Gerenciar Termos de Uso (`/admin/souenergy_notification/terms/manage`):**
    - Interface em tela cheia centralizada com quatro cards de metricas consolidadas:
        - _Versao Vigente_: Identificacao da versao ativa no momento.
        - _Total de Clientes_: Numero total de usuarios cadastrados na base.
        - _Clientes com a Versao Vigente_: Total de usuarios com aceite confirmado na versao atual.
        - _Aceites Pendentes_: Quantidade de clientes que necessitam atualizar o consentimento.
    - Formulario de publicacao de nova versao (campo de versao e campo de texto legal).
    - Botao de publicacao que grava a nova versao e dispara automaticamente uma notificacao geral para todos os usuarios.

---

### 2. Area do Usuario Comum (Storefront)

- **Redirecionamento Automatico da Raiz (`/`):**
    - Visitantes nao autenticados que acessam `http://127.0.0.1:8000/` sao redirecionados para a tela de Login.
    - Usuarios ja autenticados sao direcionados diretamente para o painel Minha Conta.

- **Tela de Login (`/customer/account/login/`):**
    - Card centralizado minimalista com identidade corporativa SouEnergy.
    - Remocao de elementos desnecessarios de loja (como vitrines, bloco de novos clientes e botoes secundários).

- **Cabecalho da Loja:**
    - Logotipo oficial SouEnergy no topo esquerdo.
    - Sino de notificacoes com contador numerico de mensagens pendentes ativas, atualizado em tempo real.
    - Links limpos para acesso a conta e encerramento de sessao.

- **Painel Minha Conta (`/customer/account/`):**
    - Interface enxuta focada em dados essenciais de cadastro.
    - Menu lateral simplificado contendo apenas:
        - _Dados da Conta_
        - _Minhas Notificacoes_

- **Central de Notificacoes do Cliente (`/souenergy_notification/customer/index/`):**
    - Listagem completa das notificacoes destinadas ao usuario ou de carater geral.
    - Botao para marcar mensagem como lida e botao de acao direta direcionando para a pagina informada no cadastro da notificacao.

- **Pagina de Termos de Uso (`/souenergy/terms/index/`):**
    - Exibicao integral do texto da versao vigente dos termos de uso.
    - Botao de confirmacao formal ("Li e Concordo com os Termos"), que grava o aceite e o timestamp auditavel no perfil do usuario.

- **Modal Inteligente de Termos de Uso:**
    - Janela modal com efeito de desfoque de fundo que aparece automaticamente no momento do login quando a versao aceita pelo usuario e inferior a versao vigente.
    - Permite direcionar o cliente imediatamente para a leitura e aceite.
    - **Mecanismo de Supressao:** Se a notificacao vinculada aos termos for desativada no Admin, o modal e automaticamente suprimido no frontend, garantindo controle ao gestor do sistema.

---

## Roteiro de Testes e Validacao Funcional (Casos de Uso)

Siga este roteiro passo a passo para validar todos os fluxos e regras de negocio implementadas no sistema:

### Caso de Teste 1: Redirecionamento da Raiz e Login do Usuario

1. Abra o navegador e acesse a URL raiz: [http://127.0.0.1:8000/](http://127.0.0.1:8000/).
2. **Resultado esperado:** O sistema identifica que nao ha sessao ativa e redireciona automaticamente para [http://127.0.0.1:8000/customer/account/login/](http://127.0.0.1:8000/customer/account/login/).
3. Informe as credenciais do cliente:
    - E-mail: `randel@souenergy.com.br`
    - Senha: `UserPassword123*`
4. Clique em **Entrar**.
5. **Resultado esperado:** O usuario e autenticado e direcionado para a tela Minha Conta. O cabecalho exibe o logotipo SouEnergy e o sino de notificacoes.

---

### Caso de Teste 2: Auditoria de Usuarios no Painel Administrativo

1. Em outra janela do navegador (ou guia anonima), acesse o Admin: [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin).
2. Autentique-se com:
    - Usuario: `admin`
    - Senha: `AdminPassword123*`
3. No menu lateral, clique em **Gerenciar Usuarios**.
4. Localize o registro de Marcos Randel na listagem.
5. **Resultado esperado:** A coluna de termos exibe a versao atual cadastrada e a coluna de data/hora registra o momento do aceite.

---

### Caso de Teste 3: Publicacao de Nova Versao dos Termos e Disparo Automatico

1. No menu lateral do Admin, clique em **Gerenciar Termos de Uso**.
2. Observe os cards de metricas no topo (versao atual, total de clientes e aceites).
3. No formulario abaixo dos cards, informe uma nova versao (ex: altere de `v1.0` para `v2.0`).
4. Atualize o texto no campo de conteudo com novas clausulas e clique em **Salvar e Notificar Usuarios**.
5. **Resultado esperado:** A pagina recarrega com mensagem de sucesso confirmando a atualizacao e os cards passam a exibir a nova versao vigente com contadores recalculados.
6. Clique no menu lateral em **Gerenciar Notificacoes**.
7. **Resultado esperado:** Uma nova notificacao geral foi criada de forma automatica pelo sistema, apontando para a URL de termos e com status `✓ Ativa`.

---

### Caso de Teste 4: Exibicao do Modal no Storefront e Confirmacao de Aceite

1. Retorne a aba do usuario comum (`randel@souenergy.com.br`) e atualize a pagina (ou realize logout e login novamente).
2. **Resultado esperado:**
    - O **Modal de Atualizacao dos Termos de Uso** e aberto em primeiro plano sobre a tela, informando que os termos foram atualizados.
    - O sino no cabecalho exibe o badge numerico incrementado com a nova notificacao.
3. No modal, clique no botao **Ler e Aceitar Novos Termos ->**.
4. O sistema abrira a pagina [http://127.0.0.1:8000/souenergy/terms/index/](http://127.0.0.1:8000/souenergy/terms/index/) com o texto completo da versao vigente.
5. Clique no botao **Li e Concordo com os Termos**.
6. **Resultado esperado:** O aceite e processado, o usuario e redirecionado para Minha Conta com mensagem de sucesso e o modal **nao e mais exibido** ao navegar ou atualizar a pagina.

---

### Caso de Teste 5: Verificacao de Auditoria no Admin Apos Aceite

1. Retorne a aba do Admin e clique novamente em **Gerenciar Usuarios**.
2. **Resultado esperado:** O badge do cliente Marcos Randel foi atualizado para `✓ v2.0 (Atualizado)` e o campo de data/hora reflete o timestamp do novo consentimento.
3. Acesse **Gerenciar Termos de Uso**.
4. **Resultado esperado:** O card _Clientes com a Versao Vigente_ foi incrementado e o card de _Aceites Pendentes_ foi reduzido.

---

### Caso de Teste 6: Desativacao de Notificacao e Supressao Inteligente do Modal

1. No Admin, acesse **Gerenciar Termos de Uso** e publique uma versao experimental (ex: `v3.0`).
2. Acesse o menu **Gerenciar Notificacoes**.
3. Localize a notificacao correspondente a versao `v3.0` e, na coluna de Acoes, clique em **Desativar**.
4. **Resultado esperado:** O status da linha muda imediatamente para `✕ Inativa`.
5. Volte a aba do usuario comum e recarregue a pagina.
6. **Resultado esperado:**
    - A notificacao inativa nao e contabilizada no sino.
    - O modal de alerta de termos **nao e exibido**, sendo suprimido devido ao status inativo da notificacao no painel.
7. Retorne ao Admin em **Gerenciar Notificacoes** e clique em **Ativar**.
8. Ao recarregar a tela do cliente, a notificacao retorna ao sino e o modal volta a ser exibido normalmente.

---

### Caso de Teste 7: Notificacao Manual e Marcacao de Leitura

1. No Admin, acesse **Gerenciar Notificacoes** e clique no botao **Nova Notificacao**.
2. Preencha os campos:
    - Titulo: `Aviso de Manutencao Programada`
    - Mensagem: `Nossos servicos passarao por melhorias no proximo fim de semana.`
    - Destinatario: Selecione _Geral (Todos os Clientes)_ ou escolha o cliente especifico.
    - Link de Acao (URL): Opcional (ex: `http://127.0.0.1:8000/`).
    - Rotulo do Botao: Opcional (ex: `Saiba Mais`).
3. Clique em **Salvar Notificacao**.
4. Acesse a tela do usuario comum: o sino de notificacoes exibira o novo contador numerico.
5. Clique no sino para visualizar ou acesse o menu **Minhas Notificacoes**.
6. Clique no botao **Marcar como lida**.
7. **Resultado esperado:** A notificacao e marcada como lida e o contador numerico do sino e reduzido instantaneamente.

---

## Otimizacoes de Performance e Ambiente

Para assegurar inicializacao rapida e consumo minimo de memoria em desenvolvimento local, modulos de e-commerce nao utilizados pelo escopo foram desativados:

| Grupo de Modulos          | Modulos Desativados                                                           | Motivo da Desativacao                                                         |
| :------------------------ | :---------------------------------------------------------------------------- | :---------------------------------------------------------------------------- |
| **Gateways de Pagamento** | `Magento_Paypal*`, `PayPal_Braintree*`, `Magento_PaymentServices*`            | O escopo do projeto nao envolve checkout nem cobranca de cartao.              |
| **Logistica e Fretes**    | `Magento_Fedex`, `Magento_Ups`, `Magento_Dhl`, `Magento_Usps`                 | Sem calculo ou despacho de mercadorias fisicas.                               |
| **Midia Comercial**       | `Magento_AdobeStock*`                                                         | Integracao com banco de imagens pago da Adobe.                                |
| **Marketing de Loja**     | `Magento_GoogleAdwords`, `Magento_GoogleAnalytics`, `Magento_GoogleOptimizer` | Rastreamento analitico comercial.                                             |
| **Seguranca 2FA**         | `Magento_TwoFactorAuth`, `Magento_AdminAdobeImsTwoFactorAuth`                 | Permite autenticacao agil no ambiente local sem necessidade de servidor SMTP. |

---

## Comandos Uteis de Manutencao

Execute estes comandos na pasta `magento_sou_energy`:

```bash
# Limpeza geral de caches do Magento
php bin/magento cache:flush

# Atualizacao de esquemas de banco e patches de dados
php -d memory_limit=2G bin/magento setup:upgrade --keep-generated

# Compilacao de DI e interceptors
php -d memory_limit=2G bin/magento setup:di:compile

# Reindexacao manual completa
php -d memory_limit=2G bin/magento indexer:reindex

# Reindexacao especifica do grid de clientes
php -d memory_limit=2G bin/magento indexer:reindex customer_grid

# Limpeza de classes geradas do modulo SouEnergy
rm -rf generated/code/SouEnergy
```

---

## APIs REST para Integracao com Frontend Desacoplado / Mobile

O backend disponibiliza endpoints REST nativos prontos para integracao com qualquer aplicacao externa desacoplada:

| Endpoint                                        | Metodo | Autenticacao | Descricao                                                                                 |
| :---------------------------------------------- | :----: | :----------: | :---------------------------------------------------------------------------------------- |
| `/rest/V1/integration/customer/token`           | `POST` |   Publica    | Login do usuario comum e emissao do token JWT/Bearer                                      |
| `/rest/V1/customers/me`                         | `GET`  | Bearer Token | Retorna o perfil completo e atributos customizados (`terms_version`, `terms_accepted_at`) |
| `/rest/V1/souenergy/notifications`              | `GET`  | Bearer Token | Retorna a lista de notificacoes ativas do usuario                                         |
| `/rest/V1/souenergy/notifications/unread-count` | `GET`  | Bearer Token | Quantidade de notificacoes nao lidas para o sino                                          |
| `/rest/V1/souenergy/notifications/:id/read`     | `POST` | Bearer Token | Marca uma notificacao individual como lida                                                |
