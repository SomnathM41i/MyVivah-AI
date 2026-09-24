<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Http\Resources\ApiKeyResource.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Resources\ApiKeyResource
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-441a22023a5b5f27762fbfe9006b5b5f0660ad54a3732aeb709b8d87e2342978',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Resources\\ApiKeyResource',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Http/Resources/ApiKeyResource.php',
      ),
    ),
    'namespace' => 'App\\Http\\Resources',
    'name' => 'App\\Http\\Resources\\ApiKeyResource',
    'shortName' => 'ApiKeyResource',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Key lifecycle metadata (Phase 3C / plan §16).
 *
 * Deliberately NEVER carries key material: only the public fingerprint (`kid`),
 * lifecycle status, and timestamps. Secrets are returned one-time on
 * create/rotate and forgotten everywhere else.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 37,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Http\\Resources\\Json\\JsonResource',
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
      'toArray' => 
      array (
        'name' => 'toArray',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 29,
            'endColumn' => 44,
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
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Resources',
        'declaringClassName' => 'App\\Http\\Resources\\ApiKeyResource',
        'implementingClassName' => 'App\\Http\\Resources\\ApiKeyResource',
        'currentClassName' => 'App\\Http\\Resources\\ApiKeyResource',
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