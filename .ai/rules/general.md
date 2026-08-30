---
paths:
  - .htaccess
---

# General

## Only whitelisted assets reach public/ (root DocumentRoot quirk)
Laragon vhost for crm-updated has its DocumentRoot at the project ROOT, not public/. The root .htaccess only proxies css|js|img|fonts|vendor|storage|build (plus favicon.ico/png/svg) into public/; ANY other static file under public/ (e.g. custom files, new build dirs) will fall through to Laravel's front controller and 404. To expose a new public/ asset, add its path to the asset RedirectRule in root .htaccess.
