<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Http\Requests\SendMessageRequest.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Requests\SendMessageRequest
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-806a1aea314c96f21acd1a9de44e2441bdb16c1de5f932f22bfddd5c0aa35029',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Requests\\SendMessageRequest',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Http/Requests/SendMessageRequest.php',
      ),
    ),
    'namespace' => 'App\\Http\\Requests',
    'name' => 'App\\Http\\Requests\\SendMessageRequest',
    'shortName' => 'SendMessageRequest',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * POST /api/v1/chat/conversations/{conversation}/messages — send a message.
 *
 * `client_message_id` is REQUIRED and is the idempotency key: retrying the same
 * logical send (same platform + conversation + client id) returns the original
 * row instead of duplicating it (docs/realtime-chat.md §Delivery Flow).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 35,
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
        'startLine' => 14,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Requests',
        'declaringClassName' => 'App\\Http\\Requests\\SendMessageRequest',
        'implementingClassName' => 'App\\Http\\Requests\\SendMessageRequest',
        'currentClassName' => 'App\\Http\\Requests\\SendMessageRequest',
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
        'startLine' => 22,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Requests',
        'declaringClassName' => 'App\\Http\\Requests\\SendMessageRequest',
        'implementingClassName' => 'App\\Http\\Requests\\SendMessageRequest',
        'currentClassName' => 'App\\Http\\Requests\\SendMessageRequest',
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