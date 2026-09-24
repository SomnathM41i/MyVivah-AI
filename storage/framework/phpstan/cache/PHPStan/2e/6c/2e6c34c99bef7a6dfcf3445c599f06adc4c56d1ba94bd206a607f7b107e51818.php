<?php declare(strict_types = 1);

// osfsl-C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../paragonie/paseto/src/JsonToken.php-PHPStan\BetterReflection\Reflection\ReflectionClass-ParagonIE\Paseto\JsonToken
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-a78b20e4825ffe6ce0a938e7d3a688842263cb98c42d69076db8a5f9f6171f7b-8.2.12-6.70.0.6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'ParagonIE\\Paseto\\JsonToken',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../paragonie/paseto/src/JsonToken.php',
      ),
    ),
    'namespace' => 'ParagonIE\\Paseto',
    'name' => 'ParagonIE\\Paseto\\JsonToken',
    'shortName' => 'JsonToken',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Class JsonToken
 * @package ParagonIE\\Paseto
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 25,
    'endLine' => 462,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'ParagonIE\\Paseto\\Traits\\RegisteredClaims',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'claims' => 
      array (
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'name' => 'claims',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 94,
            'startFilePos' => 536,
            'endTokenPos' => 95,
            'endFilePos' => 537,
          ),
        ),
        'docComment' => '/** @var array<string, mixed> */',
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'footer' => 
      array (
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'name' => 'footer',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 106,
            'startFilePos' => 596,
            'endTokenPos' => 106,
            'endFilePos' => 597,
          ),
        ),
        'docComment' => '/** @var string $footer */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 27,
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
      'build' => 
      array (
        'name' => 'build',
        'parameters' => 
        array (
          'builder' => 
          array (
            'name' => 'builder',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'ParagonIE\\Paseto\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 27,
            'endColumn' => 42,
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
            'name' => 'ParagonIE\\Paseto\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param Builder $builder
 *
 * @return Builder
 */',
        'startLine' => 40,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'get' => 
      array (
        'name' => 'get',
        'parameters' => 
        array (
          'claim' => 
          array (
            'name' => 'claim',
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
            'startLine' => 53,
            'endLine' => 53,
            'startColumn' => 25,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get any arbitrary claim.
 *
 * @param string $claim
 * @return mixed
 *
 * @throws PasetoException
 */',
        'startLine' => 53,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getAudience' => 
      array (
        'name' => 'getAudience',
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
        'docComment' => '/**
 * Get the \'aud\' claim.
 *
 * @return string
 *
 * @throws PasetoException
 */',
        'startLine' => 71,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getClaims' => 
      array (
        'name' => 'getClaims',
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
 * Get all of the claims stored in this Paseto.
 *
 * @return array
 */',
        'startLine' => 81,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getExpiration' => 
      array (
        'name' => 'getExpiration',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'DateTime',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the \'exp\' claim.
 *
 * @return DateTime
 *
 * @throws Exception
 * @throws PasetoException
 */',
        'startLine' => 94,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getFooter' => 
      array (
        'name' => 'getFooter',
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
        'docComment' => '/**
 * Get the footer as a string.
 *
 * @return string
 */',
        'startLine' => 104,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getFooterArray' => 
      array (
        'name' => 'getFooterArray',
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
 * Get the footer as an array. Assumes JSON.
 *
 * @return array
 *
 * @throws PasetoException
 */',
        'startLine' => 116,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getIssuedAt' => 
      array (
        'name' => 'getIssuedAt',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'DateTime',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the \'iat\' claim.
 *
 * @return DateTime
 *
 * @throws Exception
 * @throws PasetoException
 */',
        'startLine' => 137,
        'endLine' => 140,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getIssuer' => 
      array (
        'name' => 'getIssuer',
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
        'docComment' => '/**
 * Get the \'iss\' claim.
 *
 * @return string
 *
 * @throws PasetoException
 */',
        'startLine' => 149,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getJti' => 
      array (
        'name' => 'getJti',
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
        'docComment' => '/**
 * Get the \'jti\' claim.
 *
 * @return string
 *
 * @throws PasetoException
 */',
        'startLine' => 161,
        'endLine' => 164,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getNotBefore' => 
      array (
        'name' => 'getNotBefore',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'DateTime',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the \'nbf\' claim.
 *
 * @return DateTime
 *
 * @throws Exception
 * @throws PasetoException
 */',
        'startLine' => 174,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'getSubject' => 
      array (
        'name' => 'getSubject',
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
        'docComment' => '/**
 * Get the \'sub\' claim.
 *
 * @return string
 * @throws PasetoException
 */',
        'startLine' => 185,
        'endLine' => 188,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'set' => 
      array (
        'name' => 'set',
        'parameters' => 
        array (
          'claim' => 
          array (
            'name' => 'claim',
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
            'startLine' => 197,
            'endLine' => 197,
            'startColumn' => 25,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 197,
            'endLine' => 197,
            'startColumn' => 40,
            'endColumn' => 45,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set a claim to an arbitrary value.
 *
 * @param string $claim
 * @param mixed $value
 * @return self
 */',
        'startLine' => 197,
        'endLine' => 201,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setAudience' => 
      array (
        'name' => 'setAudience',
        'parameters' => 
        array (
          'aud' => 
          array (
            'name' => 'aud',
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
            'startLine' => 209,
            'endLine' => 209,
            'startColumn' => 33,
            'endColumn' => 43,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the \'aud\' claim.
 *
 * @param string $aud
 * @return self
 */',
        'startLine' => 209,
        'endLine' => 213,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setClaims' => 
      array (
        'name' => 'setClaims',
        'parameters' => 
        array (
          'claims' => 
          array (
            'name' => 'claims',
            'default' => NULL,
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
            'startLine' => 221,
            'endLine' => 221,
            'startColumn' => 31,
            'endColumn' => 43,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set an array of claims in one go.
 *
 * @param array<string, mixed> $claims
 * @return self
 */',
        'startLine' => 221,
        'endLine' => 225,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setExpiration' => 
      array (
        'name' => 'setExpiration',
        'parameters' => 
        array (
          'time' => 
          array (
            'name' => 'time',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 233,
                'endLine' => 233,
                'startTokenPos' => 672,
                'startFilePos' => 4588,
                'endTokenPos' => 672,
                'endFilePos' => 4591,
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
                      'name' => 'DateTimeInterface',
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
            'startLine' => 233,
            'endLine' => 233,
            'startColumn' => 35,
            'endColumn' => 64,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the \'exp\' claim.
 *
 * @param DateTimeInterface|null $time
 * @return self
 */',
        'startLine' => 233,
        'endLine' => 240,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setFooter' => 
      array (
        'name' => 'setFooter',
        'parameters' => 
        array (
          'footer' => 
          array (
            'name' => 'footer',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 248,
                'endLine' => 248,
                'startTokenPos' => 743,
                'startFilePos' => 4912,
                'endTokenPos' => 743,
                'endFilePos' => 4913,
              ),
            ),
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
            'startLine' => 248,
            'endLine' => 248,
            'startColumn' => 31,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the footer.
 *
 * @param string $footer
 * @return self
 */',
        'startLine' => 248,
        'endLine' => 252,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setFooterArray' => 
      array (
        'name' => 'setFooterArray',
        'parameters' => 
        array (
          'footer' => 
          array (
            'name' => 'footer',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 261,
                'endLine' => 261,
                'startTokenPos' => 781,
                'startFilePos' => 5208,
                'endTokenPos' => 782,
                'endFilePos' => 5209,
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
            'startLine' => 261,
            'endLine' => 261,
            'startColumn' => 36,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the footer, given an array of data. Converts to JSON.
 *
 * @param array $footer
 * @return self
 * @throws PasetoException
 */',
        'startLine' => 261,
        'endLine' => 271,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setIssuedAt' => 
      array (
        'name' => 'setIssuedAt',
        'parameters' => 
        array (
          'time' => 
          array (
            'name' => 'time',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 279,
                'endLine' => 279,
                'startTokenPos' => 857,
                'startFilePos' => 5690,
                'endTokenPos' => 857,
                'endFilePos' => 5693,
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
                      'name' => 'DateTimeInterface',
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
            'startLine' => 279,
            'endLine' => 279,
            'startColumn' => 33,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the \'iat\' claim.
 *
 * @param DateTimeInterface|null $time
 * @return self
 */',
        'startLine' => 279,
        'endLine' => 286,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setIssuer' => 
      array (
        'name' => 'setIssuer',
        'parameters' => 
        array (
          'iss' => 
          array (
            'name' => 'iss',
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
            'startLine' => 294,
            'endLine' => 294,
            'startColumn' => 31,
            'endColumn' => 41,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the \'iss\' claim.
 *
 * @param string $iss
 * @return self
 */',
        'startLine' => 294,
        'endLine' => 298,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setJti' => 
      array (
        'name' => 'setJti',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
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
            'startLine' => 306,
            'endLine' => 306,
            'startColumn' => 28,
            'endColumn' => 37,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the \'jti\' claim.
 *
 * @param string $id
 * @return self
 */',
        'startLine' => 306,
        'endLine' => 310,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setNotBefore' => 
      array (
        'name' => 'setNotBefore',
        'parameters' => 
        array (
          'time' => 
          array (
            'name' => 'time',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 318,
                'endLine' => 318,
                'startTokenPos' => 1002,
                'startFilePos' => 6475,
                'endTokenPos' => 1002,
                'endFilePos' => 6478,
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
                      'name' => 'DateTimeInterface',
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
            'startLine' => 318,
            'endLine' => 318,
            'startColumn' => 34,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the \'nbf\' claim.
 *
 * @param DateTimeInterface|null $time
 * @return self
 */',
        'startLine' => 318,
        'endLine' => 325,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'setSubject' => 
      array (
        'name' => 'setSubject',
        'parameters' => 
        array (
          'sub' => 
          array (
            'name' => 'sub',
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
            'startLine' => 333,
            'endLine' => 333,
            'startColumn' => 32,
            'endColumn' => 42,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the \'sub\' claim.
 *
 * @param string $sub
 * @return self
 */',
        'startLine' => 333,
        'endLine' => 337,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'with' => 
      array (
        'name' => 'with',
        'parameters' => 
        array (
          'claim' => 
          array (
            'name' => 'claim',
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
            'startLine' => 346,
            'endLine' => 346,
            'startColumn' => 26,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 346,
            'endLine' => 346,
            'startColumn' => 41,
            'endColumn' => 46,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed claim.
 *
 * @param string $claim
 * @param mixed $value
 * @return self
 */',
        'startLine' => 346,
        'endLine' => 349,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withAudience' => 
      array (
        'name' => 'withAudience',
        'parameters' => 
        array (
          'aud' => 
          array (
            'name' => 'aud',
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
            'startLine' => 357,
            'endLine' => 357,
            'startColumn' => 34,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed \'aud\' claim.
 *
 * @param string $aud
 * @return self
 */',
        'startLine' => 357,
        'endLine' => 360,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withClaims' => 
      array (
        'name' => 'withClaims',
        'parameters' => 
        array (
          'claims' => 
          array (
            'name' => 'claims',
            'default' => NULL,
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
            'startLine' => 368,
            'endLine' => 368,
            'startColumn' => 32,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with an array of changed claims.
 *
 * @param array<string, mixed> $claims
 * @return self
 */',
        'startLine' => 368,
        'endLine' => 371,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withExpiration' => 
      array (
        'name' => 'withExpiration',
        'parameters' => 
        array (
          'time' => 
          array (
            'name' => 'time',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 379,
                'endLine' => 379,
                'startTokenPos' => 1218,
                'startFilePos' => 7890,
                'endTokenPos' => 1218,
                'endFilePos' => 7893,
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
                      'name' => 'DateTimeInterface',
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
            'startLine' => 379,
            'endLine' => 379,
            'startColumn' => 36,
            'endColumn' => 65,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed \'exp\' claim.
 *
 * @param DateTimeInterface|null $time
 * @return self
 */',
        'startLine' => 379,
        'endLine' => 382,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withFooter' => 
      array (
        'name' => 'withFooter',
        'parameters' => 
        array (
          'footer' => 
          array (
            'name' => 'footer',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 390,
                'endLine' => 390,
                'startTokenPos' => 1256,
                'startFilePos' => 8149,
                'endTokenPos' => 1256,
                'endFilePos' => 8150,
              ),
            ),
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
            'startLine' => 390,
            'endLine' => 390,
            'startColumn' => 32,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed footer.
 *
 * @param string $footer
 * @return self
 */',
        'startLine' => 390,
        'endLine' => 393,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withFooterArray' => 
      array (
        'name' => 'withFooterArray',
        'parameters' => 
        array (
          'footer' => 
          array (
            'name' => 'footer',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 403,
                'endLine' => 403,
                'startTokenPos' => 1294,
                'startFilePos' => 8491,
                'endTokenPos' => 1295,
                'endFilePos' => 8492,
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
            'startLine' => 403,
            'endLine' => 403,
            'startColumn' => 37,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed footer,
 * representing the JSON-encoded array provided.
 *
 * @param array $footer
 * @return self
 * @throws PasetoException
 */',
        'startLine' => 403,
        'endLine' => 406,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withIssuedAt' => 
      array (
        'name' => 'withIssuedAt',
        'parameters' => 
        array (
          'time' => 
          array (
            'name' => 'time',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 414,
                'endLine' => 414,
                'startTokenPos' => 1333,
                'startFilePos' => 8781,
                'endTokenPos' => 1333,
                'endFilePos' => 8784,
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
                      'name' => 'DateTimeInterface',
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
            'startLine' => 414,
            'endLine' => 414,
            'startColumn' => 34,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed \'iat\' claim.
 *
 * @param DateTimeInterface|null $time
 * @return self
 */',
        'startLine' => 414,
        'endLine' => 417,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withIssuer' => 
      array (
        'name' => 'withIssuer',
        'parameters' => 
        array (
          'iss' => 
          array (
            'name' => 'iss',
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
            'startLine' => 425,
            'endLine' => 425,
            'startColumn' => 32,
            'endColumn' => 42,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed \'iss\' claim.
 *
 * @param string $iss
 * @return self
 */',
        'startLine' => 425,
        'endLine' => 428,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withJti' => 
      array (
        'name' => 'withJti',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
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
            'startLine' => 436,
            'endLine' => 436,
            'startColumn' => 29,
            'endColumn' => 38,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed \'jti\' claim.
 *
 * @param string $id
 * @return self
 */',
        'startLine' => 436,
        'endLine' => 439,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withNotBefore' => 
      array (
        'name' => 'withNotBefore',
        'parameters' => 
        array (
          'time' => 
          array (
            'name' => 'time',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 447,
                'endLine' => 447,
                'startTokenPos' => 1439,
                'startFilePos' => 9552,
                'endTokenPos' => 1439,
                'endFilePos' => 9555,
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
                      'name' => 'DateTimeInterface',
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
            'startLine' => 447,
            'endLine' => 447,
            'startColumn' => 35,
            'endColumn' => 64,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed \'nbf\' claim.
 *
 * @param DateTimeInterface|null $time
 * @return self
 */',
        'startLine' => 447,
        'endLine' => 450,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'aliasName' => NULL,
      ),
      'withSubject' => 
      array (
        'name' => 'withSubject',
        'parameters' => 
        array (
          'sub' => 
          array (
            'name' => 'sub',
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
            'startLine' => 458,
            'endLine' => 458,
            'startColumn' => 33,
            'endColumn' => 43,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new JsonToken instance with a changed \'sub\' claim.
 *
 * @param string $sub
 * @return self
 */',
        'startLine' => 458,
        'endLine' => 461,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'implementingClassName' => 'ParagonIE\\Paseto\\JsonToken',
        'currentClassName' => 'ParagonIE\\Paseto\\JsonToken',
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