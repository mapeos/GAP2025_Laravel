# 📱 Códigos QR Dinámicos para Diplomas

## 🎯 Descripción

Sistema de códigos QR únicos y dinámicos para verificación de diplomas. Cada diploma genera un QR único que apunta a una URL pública con información del curso.

## ✨ Características

- ✅ **QR únicos por curso**: Cada curso tiene su propio código QR
- ✅ **Verificación pública**: URL accesible sin login
- ✅ **Información completa**: Datos del curso, estado, fechas, etc.
- ✅ **Responsive**: Vistas optimizadas para móviles
- ✅ **Tracking**: Logs de escaneos para análisis
- ✅ **Fallback**: Sistema de respaldo en caso de errores
- ✅ **Seguridad**: Validación de IDs y protección contra fraudes

## 🚀 Instalación

### 1. Instalar librería QR

```bash
composer require simplesoftwareio/simple-qrcode
```

### 2. Ejecutar comando de instalación

```bash
php artisan qr:install
```

### 3. Verificar instalación

```bash
php artisan qr:install
```

## 📁 Estructura de Archivos

```
app/
├── Services/
│   └── QrCodeService.php              # Servicio principal QR
├── Http/Controllers/Public/
│   └── CursoPublicController.php      # Controlador público
└── Console/Commands/
    └── InstallQrCodeCommand.php       # Comando de instalación

config/
└── qrcode.php                         # Configuración QR

resources/views/public/cursos/
├── show.blade.php                     # Vista principal
├── not-found.blade.php                # Curso no encontrado
└── error.blade.php                    # Error general

routes/
└── web.php                            # Rutas públicas
```

## 🔧 Configuración

### Variables de entorno (.env)

```env
# URL base de la aplicación
APP_URL=https://tuapp.com

# Configuración QR (opcional)
QR_SIZE=200
QR_FORMAT=svg
QR_ERROR_CORRECTION=M
```

### Configuración avanzada (config/qrcode.php)

```php
return [
    'size' => 200,
    'format' => 'svg',
    'style' => 'square',
    'eye' => 'square',
    'margin' => 1,
    'error_correction' => 'M',
    'base_url' => env('APP_URL'),
    'storage_path' => 'qrcodes/cursos',
];
```

## 📱 Uso

### Generación automática

Los QR se generan automáticamente al crear diplomas:

```php
// En DiplomaService
$qrCode = $this->qrCodeService->generarQrParaCurso($curso->id);
```

### Generación manual

```php
use App\Services\QrCodeService;

$qrService = app(QrCodeService::class);
$qrCode = $qrService->generarQrParaCurso($cursoId);
```

### Verificación

Al escanear el QR, se accede a:
```
https://tuapp.com/cursos/{id}
```

## 🌐 URLs Públicas

### Verificar curso
```
GET /cursos/{id}
```

### API de verificación
```
GET /cursos/{id}/verificar
```

### Respuesta JSON
```json
{
    "success": true,
    "curso": {
        "id": 1,
        "titulo": "Laravel Avanzado",
        "estado": "activo",
        "fecha_inicio": "01/03/2024",
        "fecha_fin": "31/03/2024",
        "plazas": 20,
        "inscritos": 15,
        "disponibles": 5,
        "es_activo": true,
        "tiene_precio": true,
        "precio": "299.00 €"
    }
}
```

## 📊 Tracking y Logs

### Logs de escaneos

```php
Log::info('[QR_CODE] Curso consultado públicamente', [
    'curso_id' => $id,
    'titulo' => $curso->titulo,
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent()
]);
```

### Logs de errores

```php
Log::warning('[QR_CODE] Curso no encontrado', [
    'curso_id' => $id,
    'ip' => request()->ip()
]);
```

## 🎨 Personalización

### Estilos del QR

```php
// En QrCodeService
$qrCode = QrCode::size(200)
    ->format('svg')
    ->style('square')           // square, dot, round
    ->eye('square')            // square, circle
    ->margin(1)
    ->errorCorrection('M')     // L, M, Q, H
    ->generate($url);
```

### Vistas personalizadas

Modifica las vistas en `resources/views/public/cursos/` para personalizar el diseño.

## 🔒 Seguridad

### Validación de IDs

```php
// En CursoPublicController
$curso = Curso::withTrashed()->findOrFail($id);
```

### Protección contra fraudes

- Validación de existencia del curso
- Logs de intentos de acceso
- Manejo de cursos eliminados
- Rate limiting (opcional)

## 🐛 Troubleshooting

### Error: "Class QrCode not found"

```bash
composer require simplesoftwareio/simple-qrcode
composer dump-autoload
```

### Error: "QR no se genera"

```bash
# Verificar instalación
php artisan qr:install

# Verificar logs
tail -f storage/logs/laravel.log
```

### Error: "Vista no encontrada"

```bash
# Crear directorios
mkdir -p resources/views/public/cursos

# Verificar archivos
ls resources/views/public/cursos/
```

### Error: "Rutas no funcionan"

```bash
# Limpiar caché
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

## 📈 Métricas

### Comandos útiles

```bash
# Verificar QR generados
ls storage/app/public/qrcodes/cursos/

# Ver logs de escaneos
grep "QR_CODE" storage/logs/laravel.log

# Estadísticas de uso
php artisan tinker
>>> Log::where('message', 'like', '%QR_CODE%')->count();
```

## 🔄 Actualizaciones

### Actualizar configuración

```bash
php artisan config:clear
php artisan cache:clear
```

### Regenerar QR existentes

```php
// En tinker o comando personalizado
$cursos = Curso::all();
foreach ($cursos as $curso) {
    $qrService = app(QrCodeService::class);
    $qrService->generarYGuardarQr($curso->id);
}
```

## 📞 Soporte

### Logs importantes

- `storage/logs/laravel.log` - Logs generales
- `storage/app/public/qrcodes/` - QR codes generados

### Comandos de diagnóstico

```bash
# Verificar instalación
php artisan qr:install

# Probar generación
php artisan tinker
>>> app(\App\Services\QrCodeService::class)->generarQrParaCurso(1)

# Verificar rutas
php artisan route:list | grep cursos
```

---

**¡Listo! Ya tienes códigos QR dinámicos y funcionales para tus diplomas.** 📱✨ 