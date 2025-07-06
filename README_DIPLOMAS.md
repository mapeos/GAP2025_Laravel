# Sistema de Diplomas - Instalación y Uso

## 🎯 ¿Qué hace este sistema?

Genera diplomas PDF profesionales para los cursos completados usando **Browsershot** y **Puppeteer**.

## 📋 Requisitos Previos

- PHP 8.1+ con extensión **sodium** habilitada
- Node.js y npm instalados
- Composer instalado

## 🚀 Instalación Paso a Paso

### 1. Habilitar extensión sodium en PHP

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

### 2. Instalar Browsershot y Puppeteer

```bash
# En la raíz del proyecto Laravel
composer require spatie/browsershot
npm install puppeteer --save
```

### 3. Verificar instalación

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
# Verificar que esté habilitada
php -m | grep sodium

# Si no aparece, habilitar en php.ini
extension=sodium
```

### Error: "Chrome no encontrado"
```bash
# En Ubuntu/Debian
sudo apt update && sudo apt install google-chrome-stable

# En Docker, agregar al Dockerfile:
RUN apt-get update && apt-get install -y google-chrome-stable
```

### Error: "Node.js no encontrado"
```bash
# Instalar Node.js
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verificar
node --version
npm --version
```

### Error: "Timeout al generar PDF"
- Aumentar timeout en `config/browsershot.php`
- Verificar recursos del servidor
- Usar caché para mejorar rendimiento

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

## 📞 Soporte

Si tienes problemas:

1. Verifica que todas las extensiones estén habilitadas
2. Revisa los logs en `storage/logs/laravel.log`
3. Ejecuta `php artisan diploma:generate 1` para diagnosticar
4. Asegúrate de que Chrome y Node.js estén instalados

---

**¡Listo! Ya puedes generar diplomas profesionales para tus cursos.** 🎓 