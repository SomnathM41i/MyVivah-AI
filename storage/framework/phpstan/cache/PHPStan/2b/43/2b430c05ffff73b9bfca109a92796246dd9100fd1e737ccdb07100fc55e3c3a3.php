<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Http\Requests\MarkReadRequest.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Requests\MarkReadRequest
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-b6e3cb78f664aadd632581dfc3dc6aba47c61c8657393e814bbea05d9ffc1205',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Requests\\MarkReadRequest',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Http/Requests/MarkReadRequest.php',
      ),
    ),
    'namespace' => 'App\\Http\\Requests',
    'name' => 'App\\Http\\Requests\\MarkReadRequest',
    'shortName' => 'MarkReadRequest',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * POST /api/v1/chat/conversations/{conversation}/read — advance the acting
 * user\'s read cursor. Omitting `last_read_message_id` marks everything read;
 * when present it never moves the cursor backwards (docs/realtime-chat.md
 * §Unread Counts).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 27,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'App\\Http\\Requests\\ApiFormRequest',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'authorize' => 
      array (
        'name' => 'authorize',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 13,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Requests',
        'declaringClassName' => 'App\\Http\\Requests\\MarkReadRequest',
        'implementingClassName' => 'App\\Http\\Requests\\MarkReadRequest',
        'currentClassName' => 'App\\Http\\Requests\\MarkReadRequest',
        'aliasName' => NULL,
      ),
      'rules' => 
      array (
        'name' => 'rules',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array<string, mixed>
 */',
        'startLine' => 21,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Requests',
        'declaringClassName' => 'App\\Http\\Requests\\MarkReadRequest',
        'implementingClassName' => 'App\\Http\\Requests\\MarkReadRequest',
        'currentClassName' => 'App\\Http\\Requests\\MarkReadRequest',
        'aliasName' => NULL,
      ),
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