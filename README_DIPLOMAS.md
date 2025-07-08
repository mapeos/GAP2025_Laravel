# Sistema de Diplomas - Instalación y Uso

## 🎯 ¿Qué hace este sistema?

Genera diplomas PDF profesionales para los cursos completados usando **Browsershot** y **Puppeteer**.

## 📋 Requisitos Previos

- PHP 8.1+ con extensión **sodium** habilitada
- Node.js y npm instalados
- Composer instalado
- Google Chrome instalado (para Browsershot)

## 🚀 Instalación Paso a Paso

### 0. Después de hacer git pull (para nuevos desarrolladores)

```bash
# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node.js (incluye Puppeteer)
npm install
```

### 1. Configuración del Dockerfile (para contenedores Docker)

Si estás usando Docker, asegúrate de que tu `Dockerfile` incluya estas dependencias:

```dockerfile
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    gnupg \
    ca-certificates \
    fonts-liberation \
    libasound2 \
    libatk-bridge2.0-0 \
    libatk1.0-0 \
    libatspi2.0-0 \
    libcups2 \
    libdbus-1-3 \
    libdrm2 \
    libgtk-3-0 \
    libnspr4 \
    libnss3 \
    libxcomposite1 \
    libxdamage1 \
    libxrandr2 \
    libxss1 \
    libxtst6 \
    xdg-utils \
    nodejs \
    npm \
    wget \
    # Dependencias para sodium
    libsodium-dev \
    pkg-config

# Install Google Chrome
RUN wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | apt-key add - \
    && echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list \
    && apt-get update \
    && apt-get install -y google-chrome-stable \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions including sodium
RUN docker-php-ext-install zip pdo_mysql mbstring exif pcntl bcmath gd xml pcntl
RUN docker-php-ext-install sodium

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
```

### 2. Habilitar extensión sodium en PHP

**En Docker (automático con el Dockerfile anterior):**
```bash
# La extensión sodium se instala automáticamente
# Verificar que esté habilitada dentro del contenedor:
docker-compose exec www php -m | grep sodium
```

**En Windows:**
```bash
# Verificar si está habilitada
php -m | findstr sodium

# Si no aparece, editar C:\php\php.ini
# Buscar la línea: ;extension=sodium
# Quitar el punto y coma: extension=sodium
# Reiniciar terminal
```

**En Linux/Mac:**
```bash
# Verificar si está habilitada
php -m | grep sodium

# Si no aparece, instalar:
sudo apt-get install php-sodium  # Ubuntu/Debian
# o
brew install php@8.1  # Mac con Homebrew
```

### 3. Instalar Browsershot y Puppeteer

```bash
# En la raíz del proyecto Laravel
composer require spatie/browsershot
npm install puppeteer --save
```

**Nota:** Si ya hiciste `npm install` en el paso anterior, Puppeteer ya estará instalado.

### 4. Verificar instalación

```bash
# Probar que todo funciona
php artisan diploma:generate 1
```

Si se genera un PDF, ¡todo está listo!

## 🛠️ Uso Básico

### Generar diploma para un curso

```bash
# Generar diploma para curso ID 1
php artisan diploma:generate 1

# El PDF se guarda en: storage/app/diplomas/
```

### Usar en el navegador

1. Ve a la página del curso
2. Haz clic en "Generar Diploma"
3. Se descarga automáticamente

## ⚙️ Configuración (Opcional)

### Variables de entorno (.env)

```env
# Configuración de Browsershot
BROWSERSHOT_CHROME_PATH=/usr/bin/google-chrome
BROWSERSHOT_NODE_BINARY=/usr/bin/node
BROWSERSHOT_NPM_BINARY=/usr/bin/npm

# Caché de diplomas
BROWSERSHOT_CACHE_ENABLED=true
BROWSERSHOT_CACHE_TTL=3600
```

### Personalizar diplomas

**Cambiar logos/firmas:**
Editar `config/browsershot.php`

**Cambiar estilos:**
Editar `public/css/diploma.css`

**Cambiar plantillas:**
Editar archivos en `resources/views/admin/cursos/diplomas/`

## 🔧 Solución de Problemas

### Error: "Extensión sodium no disponible"
```bash
# En Docker, verificar que esté habilitada:
docker-compose exec www php -m | grep sodium

# Si no aparece, asegúrate de que el Dockerfile incluya:
RUN apt-get install -y libsodium-dev pkg-config
RUN docker-php-ext-install sodium

# En Windows:
php -m | findstr sodium
# Si no aparece, editar C:\php\php.ini y descomentar: extension=sodium

# En Linux/Mac:
php -m | grep sodium
# Si no aparece: sudo apt-get install php-sodium
```

### Error: "Chrome no encontrado"
```bash
# En Docker, verificar que Chrome esté instalado:
docker-compose exec www google-chrome --version

# Si no está instalado, asegúrate de que el Dockerfile incluya:
RUN wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | apt-key add - \
    && echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list \
    && apt-get update \
    && apt-get install -y google-chrome-stable

# En Ubuntu/Debian local:
sudo apt update && sudo apt install google-chrome-stable
```

### Error: "Node.js no encontrado"
```bash
# En Docker, verificar que Node.js esté instalado:
docker-compose exec www node --version
docker-compose exec www npm --version

# Si no está instalado, asegúrate de que el Dockerfile incluya:
RUN apt-get install -y nodejs npm

# En Ubuntu/Debian local:
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### Error: "Timeout al generar PDF"
- Aumentar timeout en `config/browsershot.php`
- Verificar recursos del servidor
- Usar caché para mejorar rendimiento
- En Docker, verificar que el contenedor tenga suficientes recursos asignados

### Error: "PDF descargado está dañado"
- Verificar que Chrome esté instalado en el contenedor Docker
- Ejecutar el comando de prueba: `docker-compose exec www php artisan diploma:generate 1`
- Verificar que el archivo se genera correctamente en `storage/app/diplomas/`
- Si el comando funciona pero la descarga web no, probar la ruta de prueba: `/admin/cursos/1/diploma/test`
- Verificar logs en `storage/logs/laravel.log`

### Error: "Composer dependencies not found"
```bash
# En Docker:
docker-compose exec www composer install

# En local:
composer install
```

### Error: "npm dependencies not found"
```bash
# En Docker:
docker-compose exec www npm install

# En local:
npm install
```

## 📁 Archivos Importantes

```
app/
├── Services/DiplomaService.php          # Lógica principal
├── Console/Commands/DiplomaCommand.php  # Comandos Artisan
└── Http/Controllers/CursoController.php # Controlador actualizado

config/
└── browsershot.php                      # Configuración

resources/views/admin/cursos/diplomas/
├── template.blade.php                   # Frente del diploma
├── template-back.blade.php              # Dorso del diploma
└── index.blade.php                      # Vista de gestión

public/css/
└── diploma.css                          # Estilos
```

## 🎨 Características del Diploma

- ✅ **Información dinámica** del curso
- ✅ **Diseño profesional** con logos y firmas
- ✅ **Números únicos** de diploma
- ✅ **Dos páginas**: Frente y dorso
- ✅ **Códigos QR** para verificación
- ✅ **Nombres de archivo** descriptivos
- ✅ **Sistema de caché** para mejor rendimiento
- ✅ **Detección automática** de Chrome/Chromium
- ✅ **Verificación de integridad** del PDF generado
- ✅ **Headers HTTP optimizados** para descarga correcta
- ✅ **Comando Artisan** para generación desde terminal

## 📞 Soporte

Si tienes problemas:

1. Verifica que todas las extensiones estén habilitadas
2. Revisa los logs en `storage/logs/laravel.log`
3. Ejecuta `php artisan diploma:generate 1` para diagnosticar
4. Asegúrate de que Chrome y Node.js estén instalados

---

**¡Listo! Ya puedes generar diplomas profesionales para tus cursos.** 🎓 