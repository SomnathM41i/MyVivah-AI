<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Http\Requests\IssueTokenRequest.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Requests\IssueTokenRequest
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-5c2e618f02b4f809ce27a94c84f11702bc76afb72db81ff423e5d8956e2c07b9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Requests\\IssueTokenRequest',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Http/Requests/IssueTokenRequest.php',
      ),
    ),
    'namespace' => 'App\\Http\\Requests',
    'name' => 'App\\Http\\Requests\\IssueTokenRequest',
    'shortName' => 'IssueTokenRequest',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 5,
    'endLine' => 44,
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
        'docComment' => '/**
 * Determine if the user is authorized to make this request.
 */',
        'startLine' => 10,
        'endLine' => 13,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Requests',
        'declaringClassName' => 'App\\Http\\Requests\\IssueTokenRequest',
        'implementingClassName' => 'App\\Http\\Requests\\IssueTokenRequest',
        'currentClassName' => 'App\\Http\\Requests\\IssueTokenRequest',
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
 * Get the validation rules that apply to the request.
 *
 * Credential exchange: `client_id` = platform public_id (ULID), `client_secret`
 * = one of the platform\'s v4.local symmetric keys (base64url). Both are required;
 * the secret is verified against the encrypted-at-rest key material.
 *
 * @return array<string, mixed>
 */',
        'startLine' => 24,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Requests',
        'declaringClassName' => 'App\\Http\\Requests\\IssueTokenRequest',
        'implementingClassName' => 'App\\Http\\Requests\\IssueTokenRequest',
        'currentClassName' => 'App\\Http\\Requests\\IssueTokenRequest',
        'aliasName' => NULL,
      ),
      'messages' => 
      array (
        'name' => 'messages',
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
 * @return array<string, string>
 */',
        'startLine' => 37,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Requests',
        'declaringClassName' => 'App\\Http\\Requests\\IssueTokenRequest',
        'implementingClassName' => 'App\\Http\\Requests\\IssueTokenRequest',
        'currentClassName' => 'App\\Http\\Requests\\IssueTokenRequest',
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