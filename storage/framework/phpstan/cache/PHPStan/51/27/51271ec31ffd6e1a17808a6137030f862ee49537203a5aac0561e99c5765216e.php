<?php declare(strict_types = 1);

// osfsl-C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../laravel/framework/src/Illuminate/Http/Exceptions/ThrottleRequestsException.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Http\Exceptions\ThrottleRequestsException
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f5f13d4d9b3ba3828dcc4aeafe00a919cff232897cf1c75018cc653c0e1b5c2c-8.2.12-6.70.0.6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Http\\Exceptions\\ThrottleRequestsException',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../laravel/framework/src/Illuminate/Http/Exceptions/ThrottleRequestsException.php',
      ),
    ),
    'namespace' => 'Illuminate\\Http\\Exceptions',
    'name' => 'Illuminate\\Http\\Exceptions\\ThrottleRequestsException',
    'shortName' => 'ThrottleRequestsException',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 22,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Symfony\\Component\\HttpKernel\\Exception\\TooManyRequestsHttpException',
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 39,
                'startFilePos' => 461,
                'endTokenPos' => 39,
                'endFilePos' => 462,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 33,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'previous' => 
          array (
            'name' => 'previous',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 49,
                'startFilePos' => 488,
                'endTokenPos' => 49,
                'endFilePos' => 491,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'Throwable',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 48,
            'endColumn' => 74,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'headers' => 
          array (
            'name' => 'headers',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 58,
                'startFilePos' => 511,
                'endTokenPos' => 59,
                'endFilePos' => 512,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 77,
            'endColumn' => 95,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'code' => 
          array (
            'name' => 'code',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 66,
                'startFilePos' => 523,
                'endTokenPos' => 66,
                'endFilePos' => 523,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 98,
            'endColumn' => 106,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new throttle requests exception instance.
 *
 * @param  string  $message
 * @param  \\Throwable|null  $previous
 * @param  array  $headers
 * @param  int  $code
 */',
        'startLine' => 18,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Http\\Exceptions',
        'declaringClassName' => 'Illuminate\\Http\\Exceptions\\ThrottleRequestsException',
        'implementingClassName' => 'Illuminate\\Http\\Exceptions\\ThrottleRequestsException',
        'currentClassName' => 'Illuminate\\Http\\Exceptions\\ThrottleRequestsException',
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