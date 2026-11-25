# Magento Sou Energy - Módulo de Autenticação e Notificações

Projeto desenvolvido em **Magento 2 Open Source** com foco na implementação de um módulo desacoplado de autenticação, configurações e sistema de notificações com interface administrativa nativa e consumo de APIs por frontend externo em **Vue.js**.

---

## 🛠️ Stack Tecnológica e Versões

Ambiente homologado e testado:

| Software | Versão Utilizada | Função no Projeto |
| :--- | :--- | :--- |
| **PHP** | `8.3.x` | Backend runtime (extensões: `intl`, `pdo_mysql`, `bcmath`, `soap`, `sodium`, `gd`, `zip`) |
| **Composer** | `2.11.x` | Gerenciador de dependências PHP |
| **MySQL** | `8.0+` | Banco de dados relacional principal |
| **OpenSearch** | `2.x` | Mecanismo de busca e indexação exigido pelo Magento 2.4+ |
| **Magento Open Source** | `2.4.7-p3` | Plataforma backend e framework de módulos |
| **Node.js** | `v23.x` | Ambiente de execução para o frontend |
| **NPM** | `10.x` | Gerenciador de pacotes do frontend |
| **Vue.js** | `3.x` *(planejado)* | Frontend para telas de usuário comum (Login, Principal e Configurações) |

---

## 🖥️ Compatibilidade por Sistema Operacional

O Magento 2 é um framework de padrão Unix/POSIX. Veja como inicializar os serviços de acordo com o seu sistema operacional:

### 1. macOS (Apple Silicon / Intel)
Gerenciado via Homebrew:
```bash
brew services start mysql
brew services start opensearch
```

### 2. Linux (Ubuntu / Debian / CentOS)
Gerenciado nativamente via `systemd`:
```bash
# Iniciar serviços
sudo systemctl start mysql
sudo systemctl start opensearch

# Extensões PHP recomendadas no Ubuntu:
# sudo apt install php8.3-cli php8.3-common php8.3-intl php8.3-mysql php8.3-bcmath php8.3-soap php8.3-gd php8.3-zip php8.3-curl php8.3-xml
```

### 3. Windows (Atenção)
O Magento 2 **não oferece suporte a execução nativa direta no Windows CMD/PowerShell** devido a incompatibilidade com links simbólicos (*symlinks*), separadores de diretório e permissões de arquivos.

* **Solução Oficial / Recomendada:** Utilizar o **WSL 2 (Windows Subsystem for Linux)** com distribuição Ubuntu.
* Dentro do WSL 2, o ambiente se comporta exatamente como um **Linux nativo** e todos os comandos deste documento funcionam perfeitamente.

---

## 🚀 Requisitos e Inicialização dos Serviços

Antes de executar a aplicação, confirme que o banco e o motor de busca estão ativos:

* **MySQL:** Conexão disponível na porta `3306`.
* **OpenSearch:** Conexão ativa na porta `9200`.
  ```bash
  # Teste rápido de status do OpenSearch:
  curl http://localhost:9200
  ```

---

## ⚙️ Instalação do Projeto

### 1. Criar o Banco de Dados no MySQL
No terminal ou via interface gráfica (Sequel Ace, TablePlus ou DBeaver):
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS magento_sou_energy;"
```

### 2. Instalar as Dependências do Composer
Dentro da pasta `magento_sou_energy`:
```bash
composer install
```
*(Caso seja a primeira instalação na sua máquina, informe sua Public Key como Username e Private Key como Password geradas no portal Adobe Marketplace).*

### 3. Executar o Instalador do Magento
Execute o script de configuração oficial para criar a estrutura de tabelas, chaves de criptografia e o administrador inicial:

```bash
bin/magento setup:install \
  --base-url="http://127.0.0.1:8000/" \
  --db-host="127.0.0.1" \
  --db-name="magento_sou_energy" \
  --db-user="root" \
  --db-password="" \
  --search-engine="opensearch" \
  --opensearch-host="127.0.0.1" \
  --opensearch-port="9200" \
  --admin-firstname="Admin" \
  --admin-lastname="SouEnergy" \
  --admin-email="admin@souenergy.com.br" \
  --admin-user="admin" \
  --admin-password="AdminPassword123*" \
  --backend-frontname="admin" \
  --language="pt_BR" \
  --currency="BRL" \
  --timezone="America/Sao_Paulo" \
  --use-rewrites=1
```

### 4. Desativar 2FA em Ambiente Local
Para facilitar o desenvolvimento e testes locais sem necessidade de servidor de e-mail ativo:
```bash
bin/magento module:disable Magento_TwoFactorAuth
bin/magento cache:flush
```

---

## 💻 Executando o Servidor Local

Inicie o servidor de desenvolvimento embutido do PHP apontando para o diretório público:

```bash
php -S 127.0.0.1:8000 -t pub/ phpserver/router.php
```

---

## 🔗 Acessos e Credenciais

* **URL Base da Aplicação / APIs:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
* **Painel Administrativo:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
  * **Usuário:** `admin`
  * **Senha:** `AdminPassword123*`

---

## 🏛️ Arquitetura do Projeto

* **Backend (Magento 2):** Responsável por autenticação, regras de negócio, persistência no MySQL, gerenciamento administrativo de notificações e exposição de endpoints REST.
* **Frontend Usuário (Vue.js):** Interface SPA desacoplada consumindo as APIs do Magento para login, exibição de notificações e configurações de perfil.
* **Admin (Magento Native Grid/Forms):** Gestão administrativa de usuários e cadastro de disparos de notificações diretamente pelo painel do Magento.
