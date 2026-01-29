# Guia de Configuração: Biblioteca Digital Unicentro

Este guia descreve como configurar e rodar o projeto em um novo computador.

## Pré-requisitos
- **Git** instalado.
- **Docker Desktop** (Windows/Mac) ou **Docker Engine** (Linux) instalado e rodando.
- Opcional: PHP e Composer instalados localmente (se não tiver, usaremos um container Docker auxiliar).

## Passo a Passo

### 1. Clonar o Repositório
Abra o terminal e clone o projeto:
```bash
git clone <URL_DO_SEU_REPOSITORIO> biblioteca-backend
cd biblioteca-backend
```

### 2. Configurar Variáveis de Ambiente
Copie o arquivo de exemplo para criar o seu `.env`:
```bash
cp .env.example .env
```
*Dica: Se estiver no Windows (PowerShell), use `copy .env.example .env`.*

Não precisa alterar nada no `.env` para rodar localmente com Docker, as configurações padrão do Sail já funcionam.

### 3. Instalar Dependências do PHP (Composer)
Se você **NÃO** tem PHP/Composer instalado na máquina, rode este comando Docker para instalar as dependências:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

*Se você já tem PHP e Composer instalados localmente, basta rodar `composer install`.*

### 4. Iniciar os Containers (Laravel Sail)
Agora suba o ambiente de desenvolvimento:
```bash
./vendor/bin/sail up -d
```
*Isso pode demorar alguns minutos na primeira vez para baixar as imagens.*

### 5. Configurar o Projeto
Com os containers rodando, execute os comandos abaixo para finalizar a configuração:

```bash
# Gerar chave de criptografia do Laravel
./vendor/bin/sail artisan key:generate

# Criar o banco de dados e rodar migrações (e seeds se tiver)
./vendor/bin/sail artisan migrate --seed

# Criar link simbólico para imagens (storage)
./vendor/bin/sail artisan storage:link
```

### 6. Instalar Dependências do Frontend (Assets)
Instale e compile o CSS/JS (Tailwind, Alpine, etc):

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

## ✅ Pronto!
O projeto deve estar rodando em:
- **Site:** http://localhost
- **Admin:** http://localhost/admin
- **Email (Mailpit):** http://localhost:8025 (para ver emails de teste)

## Comandos Úteis no Dia a Dia

- **Parar o servidor:** `./vendor/bin/sail stop`
- **Reiniciar:** `./vendor/bin/sail restart`
- **Rodar migrações:** `./vendor/bin/sail artisan migrate`
- **Compilar assets (dev):** `./vendor/bin/sail npm run dev` (hot reload)
