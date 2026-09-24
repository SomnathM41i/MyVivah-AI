<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Console\Commands\ChatPresenceSweepCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\ChatPresenceSweepCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-3267226ec366cdd0e1ca075c9d3306dd438e31cb6cd00ff903a89f719cd05dd3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\ChatPresenceSweepCommand',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Console/Commands/ChatPresenceSweepCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands',
    'name' => 'App\\Console\\Commands\\ChatPresenceSweepCommand',
    'shortName' => 'ChatPresenceSweepCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Mark stale-online users offline and broadcast the transition (Phase 3E).
 *
 * Shared-hosting friendly: DB-backed staleness (no Redis), one transaction per
 * row, bounded batch. Expected run from cron every minute:
 *   * * * * * php /path/to/artisan chat:presence-sweep
 * Optional --platform=<public_id|slug> narrows the sweep to one platform.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 44,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
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
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\ChatPresenceSweepCommand',
        'implementingClassName' => 'App\\Console\\Commands\\ChatPresenceSweepCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'chat:presence-sweep
                            {--platform= : optional platform public_id or slug to limit the sweep}\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 20,
            'startTokenPos' => 40,
            'startFilePos' => 566,
            'endTokenPos' => 40,
            'endFilePos' => 685,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 100,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\ChatPresenceSweepCommand',
        'implementingClassName' => 'App\\Console\\Commands\\ChatPresenceSweepCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Mark stale-online chat users offline and broadcast user.offline.\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 49,
            'startFilePos' => 718,
            'endTokenPos' => 49,
            'endFilePos' => 783,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 96,
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
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'presence' => 
          array (
            'name' => 'presence',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\PresenceService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 28,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 24,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands',
        'declaringClassName' => 'App\\Console\\Commands\\ChatPresenceSweepCommand',
        'implementingClassName' => 'App\\Console\\Commands\\ChatPresenceSweepCommand',
        'currentClassName' => 'App\\Console\\Commands\\ChatPresenceSweepCommand',
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