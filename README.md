# Larabrandly

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kemok-repos/larabrandly.svg?style=flat-square)](https://packagist.org/packages/kemok-repos/larabrandly)
[![Tests](https://github.com/Kemok-Repos/larabrandly/actions/workflows/tests.yml/badge.svg)](https://github.com/Kemok-Repos/larabrandly/actions/workflows/tests.yml)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/kemok-repos/larabrandly.svg?style=flat-square)](https://packagist.org/packages/kemok-repos/larabrandly)
[![PHP Version](https://img.shields.io/packagist/php-v/kemok-repos/larabrandly.svg?style=flat-square)](https://packagist.org/packages/kemok-repos/larabrandly)

Un paquete de Laravel para la integración con la API de Rebrandly. Permite crear, gestionar y rastrear enlaces cortos de manera sencilla.

## Características

- ✅ **Enlaces cortos**: Crear, actualizar, eliminar y listar
- ✅ **Gestión de cuenta**: Obtener detalles de la cuenta
- ✅ **Sistema de Tags**: Crear, editar, eliminar y listar tags
- ✅ **Asociación de Tags**: Attach/detach tags a enlaces
- ✅ **Filtros avanzados**: Filtrado completo de enlaces
- ✅ **Gestión de errores**: Manejo robusto de errores de API
- ✅ **Testing completo**: Pruebas unitarias y de integración
- ✅ **Soporte Laravel**: Compatible con Laravel 9, 10, 11 y 12
- ✅ **Facades múltiples**: Para enlaces y tags por separado

## Instalación

Puedes instalar el paquete vía composer:

```bash
composer require kemok-repos/larabrandly
```

Publica el archivo de configuración con:

```bash
php artisan vendor:publish --tag="larabrandly-config"
```

Este es el contenido del archivo de configuración publicado:

```php
return [
    'api_key' => env('REBRANDLY_API_KEY'),
    'default_domain' => env('REBRANDLY_DEFAULT_DOMAIN'),
    'http' => [
        'timeout' => env('REBRANDLY_HTTP_TIMEOUT', 30),
        'retry_times' => env('REBRANDLY_RETRY_TIMES', 3),
        'retry_delay' => env('REBRANDLY_RETRY_DELAY', 100),
    ],
];
```

## Configuración

Agrega tu API key de Rebrandly al archivo `.env`:

```env
REBRANDLY_API_KEY=tu_api_key_aqui
REBRANDLY_DEFAULT_DOMAIN=tu-dominio.com
```

## Uso

### Usando el Facade

```php
use KemokRepos\Larabrandly\Facades\Rebrandly;
use KemokRepos\Larabrandly\Facades\RebrandlyTags;
use KemokRepos\Larabrandly\Data\CreateLinkData;
use KemokRepos\Larabrandly\Data\UpdateLinkData;
use KemokRepos\Larabrandly\Data\CreateTagData;
use KemokRepos\Larabrandly\Data\LinkFilters;

// Obtener detalles de la cuenta
$account = Rebrandly::getAccount();
echo $account->username;
echo $account->email;

// Crear un enlace
$createData = new CreateLinkData(
    destination: 'https://example.com',
    slashtag: 'mi-enlace',
    title: 'Mi Enlace Personalizado',
    description: 'Descripción del enlace',
    tags: ['marketing', 'campaña']
);

$link = Rebrandly::createLink($createData);
echo $link->shortUrl; // https://tu-dominio.com/mi-enlace

// Obtener un enlace
$link = Rebrandly::getLink('abc123');

// Actualizar un enlace
$updateData = new UpdateLinkData(
    title: 'Nuevo Título',
    favourite: true
);

$updatedLink = Rebrandly::updateLink('abc123', $updateData);

// Eliminar un enlace
$deleted = Rebrandly::deleteLink('abc123');

// Listar enlaces con filtros avanzados
$filters = new LinkFilters(
    limit: 20,
    favourite: true,
    tags: ['marketing'],
    orderBy: 'createdAt',
    orderDir: 'desc'
);

$links = Rebrandly::listLinks($filters);

// O usar array simple
$links = Rebrandly::listLinks(['limit' => 10, 'favourite' => true]);

// Gestión de Tags
$tagData = new CreateTagData(
    name: 'Marketing',
    color: '#ff6b35'
);

$tag = RebrandlyTags::createTag($tagData);

// Listar todos los tags
$tags = RebrandlyTags::listTags();

// Asociar tag a enlace
$attached = Rebrandly::attachTagToLink('link123', 'tag456');

// Obtener tags de un enlace
$linkTags = Rebrandly::getLinkTags('link123');

// Obtener todos los enlaces asociados a un tag
$tagLinks = Rebrandly::getTagLinks('tag456');
// O con filtros
$tagLinks = RebrandlyTags::getTagLinks('tag456', ['limit' => 10, 'orderBy' => 'clicks']);

// Desasociar tag de enlace
$detached = Rebrandly::detachTagFromLink('link123', 'tag456');
```

### Usando Inyección de Dependencias

```php
use KemokRepos\Larabrandly\Services\RebrandlyService;
use KemokRepos\Larabrandly\Services\TagService;
use KemokRepos\Larabrandly\Data\CreateLinkData;
use KemokRepos\Larabrandly\Data\CreateTagData;

class LinkController extends Controller
{
    public function __construct(
        private RebrandlyService $rebrandly,
        private TagService $tagService
    ) {}

    public function store(Request $request)
    {
        $createData = new CreateLinkData(
            destination: $request->destination,
            title: $request->title
        );

        $link = $this->rebrandly->createLink($createData);

        return response()->json($link);
    }

    public function getAccountInfo()
    {
        $account = $this->rebrandly->getAccount();

        return response()->json([
            'username' => $account->username,
            'email' => $account->email,
            'usage' => $account->usage,
            'limits' => $account->limits,
        ]);
    }

    public function attachTag(Request $request, string $linkId)
    {
        $tagId = $request->input('tag_id');
        
        $attached = $this->rebrandly->attachTagToLink($linkId, $tagId);

        return response()->json(['success' => $attached]);
    }

    public function getTagLinks(string $tagId)
    {
        $links = $this->tagService->getTagLinks($tagId, [
            'limit' => 50,
            'orderBy' => 'clicks',
            'orderDir' => 'desc'
        ]);

        return response()->json([
            'tag_id' => $tagId,
            'total_links' => count($links),
            'links' => $links
        ]);
    }
}
```

## DTOs Disponibles

### AccountData

Información de la cuenta:

```php
$account = Rebrandly::getAccount();

echo $account->id;           // ID de usuario
echo $account->username;     // Nombre de usuario
echo $account->email;        // Email
echo $account->fullName;     // Nombre completo
echo $account->subscription; // Información de suscripción
echo $account->usage;        // Uso actual
echo $account->limits;       // Límites de la cuenta
```

### CreateLinkData

Para crear nuevos enlaces:

```php
$createData = new CreateLinkData(
    destination: 'https://example.com',    // Requerido
    slashtag: 'custom-slug',              // Opcional
    title: 'Link Title',                  // Opcional
    description: 'Link description',       // Opcional
    domain: 'custom-domain.com',          // Opcional
    tags: ['tag1', 'tag2'],              // Opcional
    favourite: true                       // Opcional
);
```

### UpdateLinkData

Para actualizar enlaces existentes:

```php
$updateData = new UpdateLinkData(
    destination: 'https://new-destination.com',
    title: 'New Title',
    description: 'New description',
    tags: ['new-tag'],
    favourite: false
);
```

### LinkData

Objeto de respuesta que contiene toda la información del enlace:

```php
$link = Rebrandly::getLink('abc123');

echo $link->id;          // ID del enlace
echo $link->title;       // Título
echo $link->destination; // URL destino
echo $link->shortUrl;    // URL corta
echo $link->clicks;      // Número de clicks
echo $link->favourite;   // Si es favorito
// ... más propiedades
```

### LinkFilters

Para filtrar la lista de enlaces:

```php
$filters = new LinkFilters(
    limit: 50,                                    // Límite de resultados
    offset: 0,                                    // Offset para paginación
    orderBy: 'createdAt',                        // Campo para ordenar
    orderDir: 'desc',                            // Dirección del orden
    domain: 'my-domain.com',                     // Filtrar por dominio
    favourite: true,                             // Solo favoritos
    tags: ['marketing', 'campaign'],             // Filtrar por tags
    slashtag: 'partial-slug',                    // Buscar por slashtag
    title: 'Search Title',                       // Buscar por título
    createdAfter: new DateTimeImmutable('2023-01-01'),  // Creado después de
    createdBefore: new DateTimeImmutable('2023-12-31'), // Creado antes de
    modifiedAfter: new DateTimeImmutable('2023-06-01'), // Modificado después de
    modifiedBefore: new DateTimeImmutable('2023-12-31') // Modificado antes de
);

$links = Rebrandly::listLinks($filters);
```

### CreateTagData y UpdateTagData

Para gestionar tags:

```php
// Crear tag
$createTag = new CreateTagData(
    name: 'Marketing',      // Requerido
    color: '#ff6b35'        // Opcional - color hexadecimal
);

// Actualizar tag
$updateTag = new UpdateTagData(
    name: 'New Name',       // Opcional
    color: '#00ff00'        // Opcional
);
```

### TagData

Información de un tag:

```php
$tag = RebrandlyTags::getTag('tag123');

echo $tag->id;          // ID del tag
echo $tag->name;        // Nombre
echo $tag->color;       // Color (hex)
echo $tag->linksCount;  // Cantidad de enlaces asociados

// Para obtener los enlaces de un tag
$tagLinks = RebrandlyTags::getTagLinks($tag->id);
foreach ($tagLinks as $link) {
    echo $link->title . ' - ' . $link->clicks . ' clicks';
}
```

## Casos de Uso Comunes

### Gestión Completa de Tags y Enlaces

```php
// Crear un nuevo tag para una campaña
$campaignTag = RebrandlyTags::createTag(new CreateTagData(
    name: 'Black Friday 2024',
    color: '#000000'
));

// Crear varios enlaces para la campaña
$links = [
    'https://shop.com/deals/electronics',
    'https://shop.com/deals/clothing', 
    'https://shop.com/deals/home'
];

$createdLinks = [];
foreach ($links as $index => $url) {
    $linkData = new CreateLinkData(
        destination: $url,
        slashtag: 'bf-deal-' . ($index + 1),
        title: 'Black Friday Deal ' . ($index + 1)
    );
    
    $link = Rebrandly::createLink($linkData);
    $createdLinks[] = $link;
    
    // Asociar el tag a cada enlace
    Rebrandly::attachTagToLink($link->id, $campaignTag->id);
}

// Obtener estadísticas de la campaña
$campaignLinks = RebrandlyTags::getTagLinks($campaignTag->id);
$totalClicks = array_sum(array_column($campaignLinks, 'clicks'));

echo "Campaña: {$campaignTag->name}";
echo "Total de enlaces: " . count($campaignLinks);
echo "Total de clicks: {$totalClicks}";
```

### Filtrado Avanzado de Enlaces

```php
// Buscar enlaces populares de los últimos 30 días
$filters = new LinkFilters(
    limit: 20,
    orderBy: 'clicks',
    orderDir: 'desc',
    favourite: true,
    createdAfter: new DateTimeImmutable('-30 days'),
    tags: ['marketing', 'social-media']
);

$popularLinks = Rebrandly::listLinks($filters);

// Mostrar estadísticas
foreach ($popularLinks as $link) {
    echo "{$link->title}: {$link->clicks} clicks - {$link->shortUrl}";
}
```

## Gestión de Errores

El paquete maneja automáticamente los errores de la API de Rebrandly:

```php
use KemokRepos\Larabrandly\Exceptions\RebrandlyException;

try {
    $link = Rebrandly::createLink($createData);
} catch (RebrandlyException $e) {
    echo $e->getMessage();
    
    // Para errores de validación, obtén el contexto
    if ($e->getCode() === 422) {
        $errors = $e->getContext()['errors'] ?? [];
        // Manejar errores de validación
    }
}
```

## Testing

```bash
# Ejecutar todas las pruebas
composer test

# Generar reporte de cobertura en HTML (requiere Xdebug o PCOV)
composer test-coverage

# Ver cobertura en la terminal (requiere Xdebug o PCOV)
composer test-coverage-text

# Generar archivo XML de cobertura para CI/CD (requiere Xdebug o PCOV)
composer test-coverage-clover
```

### Configurar Cobertura de Código

Para generar reportes de cobertura, necesitas instalar **Xdebug** o **PCOV**:

#### Opción 1: Instalar Xdebug
```bash
# macOS (con Homebrew)
brew install php@8.1-xdebug  # o tu versión de PHP

# Ubuntu/Debian
sudo apt-get install php-xdebug

# CentOS/RHEL
sudo yum install php-xdebug
```

#### Opción 2: Instalar PCOV (más rápido para cobertura)
```bash
# Con PECL
pecl install pcov

# Agregar a php.ini
echo "extension=pcov.so" >> php.ini
```

Una vez instalado cualquiera de los dos, los comandos de cobertura funcionarán correctamente.

## Análisis de Código

```bash
# Análisis estático con PHPStan
composer analyse
# o
composer phpstan

# Verificar formato del código
composer check-format

# Aplicar formato automáticamente
composer format

# Ejecutar todas las verificaciones de calidad
composer quality
```

## Changelog

Por favor consulta [CHANGELOG](CHANGELOG.md) para más información sobre los cambios recientes.

## Contribuciones

Por favor consulta [CONTRIBUTING](CONTRIBUTING.md) para más detalles.

## Seguridad

Si descubres algún problema de seguridad, por favor envía un email a security@kemok-repos.com en lugar de usar el issue tracker.

## Créditos

- [Kemok Repos](https://github.com/kemok-repos)
- [Todos los Contribuidores](../../contributors)

## Licencia

La Licencia MIT (MIT). Por favor consulta [License File](LICENSE.md) para más información.