<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Services\WidgetRealtimeConfig.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\WidgetRealtimeConfig
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-dd5ca0dfc087a475ed89ff22f430ba91497cf06825b7b92d6c65567d7469434a',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\WidgetRealtimeConfig',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Services/WidgetRealtimeConfig.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\WidgetRealtimeConfig',
    'shortName' => 'WidgetRealtimeConfig',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Realtime connection coordinates for the widget browser client (Phase 4).
 *
 * Everything here is PUBLIC-safe (app key, host, port, path, scheme). The
 * symmetric app secret NEVER leaves the server — the browser subscribes through
 * the existing socket/auth endpoint which requires a valid widget session token.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 97,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'BANNED_CONNECTION_MODE' => 
      array (
        'declaringClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'implementingClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'name' => 'BANNED_CONNECTION_MODE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'null\'',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 14,
            'startTokenPos' => 23,
            'startFilePos' => 431,
            'endTokenPos' => 23,
            'endFilePos' => 436,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'CONNECTION_MODES' => 
      array (
        'declaringClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'implementingClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'name' => 'CONNECTION_MODES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'reverb\', \'pusher\', \'soketi\']',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 34,
            'startFilePos' => 477,
            'endTokenPos' => 42,
            'endFilePos' => 506,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 68,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'enabled' => 
      array (
        'name' => 'enabled',
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
        'docComment' => '/**
 * Whether the widget browser client should attempt a live socket.
 */',
        'startLine' => 21,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'implementingClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'currentClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'aliasName' => NULL,
      ),
      'connectionMode' => 
      array (
        'name' => 'connectionMode',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 28,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'implementingClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'currentClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'aliasName' => NULL,
      ),
      'appKey' => 
      array (
        'name' => 'appKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 42,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'implementingClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'currentClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'aliasName' => NULL,
      ),
      'for' => 
      array (
        'name' => 'for',
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
 * Public, browser-safe realtime coordinates (never the app secret).
 *
 * @return array{
 *     enabled: bool,
 *     connection: string,
 *     app_key: string,
 *     scheme: string,
 *     host: string,
 *     port: int,
 *     path: string,
 * }
 */',
        'startLine' => 60,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'implementingClassName' => 'App\\Services\\WidgetRealtimeConfig',
        'currentClassName' => 'App\\Services\\WidgetRealtimeConfig',
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