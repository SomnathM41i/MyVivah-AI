<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\ContactMessage.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\ContactMessage
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-7ba6d01557c1e07709ab6fda2eb8f3ae994312a1826041b72721c2e8d9e15c37',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\ContactMessage',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/ContactMessage.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\ContactMessage',
    'shortName' => 'ContactMessage',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Public-site contact form submission (Phase 5A).
 *
 * Append-mostly inbox for messages sent through the public `/contact` form.
 * Internal-only table (no `public_id`, no soft-delete). Stores a hashed IP
 * (never the raw IP) for abuse triage; see migration comments.
 *
 * @property int $id
 * @property string $name
 * @property string|null $company
 * @property string $email
 * @property string|null $phone
 * @property string $message
 * @property string|null $ip_hash
 * @property string|null $source_url
 * @property Carbon|null $created_at
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 26,
    'endLine' => 49,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
      'UPDATED_AT' => 
      array (
        'declaringClassName' => 'App\\Models\\ContactMessage',
        'implementingClassName' => 'App\\Models\\ContactMessage',
        'name' => 'UPDATED_AT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 49,
            'startFilePos' => 887,
            'endTokenPos' => 49,
            'endFilePos' => 890,
          ),
        ),
        'docComment' => '/**
 * Contact submissions are append-only (no updated_at needed).
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\ContactMessage',
        'implementingClassName' => 'App\\Models\\ContactMessage',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'company\', \'email\', \'phone\', \'message\', \'ip_hash\', \'source_url\']',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 48,
            'startTokenPos' => 60,
            'startFilePos' => 1016,
            'endTokenPos' => 83,
            'endFilePos' => 1151,
          ),
        ),
        'docComment' => '/**
 * The attributes that are mass assignable.
 *
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));