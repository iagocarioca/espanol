# Tema Espanol - WordPress

Tema WordPress com deploy automatico via GitHub Actions.

## Estrutura

```
.
├── .github/workflows/deploy.yml   # Deploy automatico
└── espanol/                       # O tema (vai para wp-content/themes/espanol)
    ├── style.css
    ├── functions.php
    ├── inc/
    ├── js/
    └── templates/
```

## Como funciona o deploy

Todo `git push` na branch `main` dispara o workflow, que:

1. Valida a sintaxe de todos os arquivos PHP (se houver erro, o deploy para).
2. Envia via FTP apenas os arquivos que mudaram para o servidor.

Tambem da para disparar manualmente em **Actions > Deploy tema para o WordPress > Run workflow**.

## Configuracao dos Secrets

No GitHub: **Settings > Secrets and variables > Actions > New repository secret**.

Crie os 5 secrets abaixo:

| Secret | Valor |
|---|---|
| `FTP_SERVER` | IP ou host do servidor FTP |
| `FTP_PORT` | Porta do FTP |
| `FTP_USERNAME` | Usuario do FTP |
| `FTP_PASSWORD` | Senha do FTP |
| `FTP_REMOTE_DIR` | Caminho remoto do tema, com barra no final |

O `FTP_REMOTE_DIR` precisa terminar com `/` e apontar para a pasta do tema, por exemplo:

```
/public_html/wp-content/themes/espanol/
```

## Seguranca

- Nenhuma credencial fica no repositorio. Tudo vem dos Secrets do GitHub.
- O repositorio e publico: nunca faca commit de senha, `.env` ou `wp-config.php`.
- Os Secrets ficam mascarados nos logs do Actions.

## Primeiro deploy

Antes do primeiro push, confirme que a pasta do tema ja existe no servidor
(`wp-content/themes/espanol/`). Depois:

```bash
git add .
git commit -m "Configura deploy automatico"
git push origin main
```

Acompanhe em **Actions** no GitHub.
