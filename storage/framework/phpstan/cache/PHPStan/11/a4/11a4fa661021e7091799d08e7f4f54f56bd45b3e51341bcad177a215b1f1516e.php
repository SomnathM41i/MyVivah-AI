<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Support\TokenKeyContext.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Support\TokenKeyContext
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-7844c13f2b1578b15a9fbfcfdd369807177af8e8097e33b7753246b99a0e27b8',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Support\\TokenKeyContext',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Support/TokenKeyContext.php',
      ),
    ),
    'namespace' => 'App\\Support',
    'name' => 'App\\Support\\TokenKeyContext',
    'shortName' => 'TokenKeyContext',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Best-effort platform/key context from an UNVERIFIED bearer token.
 *
 * Used only for audit attribution on REJECTED requests: the footer `kid` is
 * unwrapped (non-cryptographic) and the owning key/platform loaded so denial
 * rows stay platform-scoped. Never trust the payload — the footer is visibly
 * signed, but we never act on ANY claim here beyond finding the key row.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 46,
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
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'resolve' => 
      array (
        'name' => 'resolve',
        'parameters' => 
        array (
          'token' => 
          array (
            'name' => 'token',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 36,
            'endColumn' => 48,
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
 * @return array{0: Platform|null, 1: PlatformApiKey|null}
 */',
        'startLine' => 22,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support',
        'declaringClassName' => 'App\\Support\\TokenKeyContext',
        'implementingClassName' => 'App\\Support\\TokenKeyContext',
        'currentClassName' => 'App\\Support\\TokenKeyContext',
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