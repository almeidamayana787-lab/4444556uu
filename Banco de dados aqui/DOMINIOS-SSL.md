# Documentação: Configuração de Domínio e SSL

Este documento descreve os passos realizados para configurar o domínio `499-bet.mooo.com` e o certificado SSL (HTTPS) no servidor VPS.

## 1. Configuração do Domínio (DNS)

O domínio foi configurado através do serviço **Afraid (FreeDNS)**.
- **Domínio:** `499-bet.mooo.com`
- **Destino (IP):** `163.245.218.28` (IP da VPS)
- **Tipo:** Registro A (Host)

## 2. Configuração do Laravel (`.env`)

No diretório do projeto na VPS (`/var/www/qgbet`), o arquivo `.env` foi atualizado para refletir o novo endereço seguro:

```env
APP_URL=https://499-bet.mooo.com
```

Após a alteração, os caches do Laravel foram limpos:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 3. Configuração do Servidor Web (Apache)

### VirtualHost HTTP (Porta 80)
O arquivo `/etc/apache2/sites-enabled/000-default.conf` foi editado para incluir o nome do servidor:
```apache
<VirtualHost *:80>
    ServerName 499-bet.mooo.com
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/qgbet/public
    # ... outras configurações ...
</VirtualHost>
```

### Instalação do SSL (Certbot)
Foi utilizado o **Certbot** com o plugin do Apache para obter o certificado gratuito da **Let's Encrypt**:
```bash
apt update
apt install -y certbot python3-certbot-apache
certbot --apache -d 499-bet.mooo.com
```

O Certbot criou automaticamente o arquivo de configuração para HTTPS:
- `/etc/apache2/sites-enabled/000-default-le-ssl.conf`

## 4. Manutenção e Renovação

O Certbot configura automaticamente uma tarefa (cron) para renovar o certificado. Para testar a renovação manualmente no futuro, use:
```bash
certbot renew --dry-run
```

## 5. Acesso Final

O site está configurado para redirecionar automaticamente de HTTP para HTTPS:
- **URL Final:** [https://499-bet.mooo.com](https://499-bet.mooo.com)
