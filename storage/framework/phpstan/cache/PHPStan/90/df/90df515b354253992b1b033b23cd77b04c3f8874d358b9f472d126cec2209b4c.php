<?php declare(strict_types = 1);

// osfsl-C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../paragonie/paseto/src/ProtocolInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-ParagonIE\Paseto\ProtocolInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-64c051108c6c82beb00cbc895da920998210e0e79c6ab3a459f748d9c7d9fcf5-8.2.12-6.70.0.6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../paragonie/paseto/src/ProtocolInterface.php',
      ),
    ),
    'namespace' => 'ParagonIE\\Paseto',
    'name' => 'ParagonIE\\Paseto\\ProtocolInterface',
    'shortName' => 'ProtocolInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Interface ProtocolInterface
 * @package ParagonIE\\Paseto
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 115,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Must be constructable with no arguments so an instance may be passed
 * around in a type safe way.
 */',
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 34,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'aliasName' => NULL,
      ),
      'header' => 
      array (
        'name' => 'header',
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
 * A unique header string with which the protocol can be identified.
 *
 * @return string
 */',
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 44,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'aliasName' => NULL,
      ),
      'supportsImplicitAssertions' => 
      array (
        'name' => 'supportsImplicitAssertions',
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
 * Does this protocol support implicit assertions?
 *
 * @return bool
 */',
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 62,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'aliasName' => NULL,
      ),
      'generateAsymmetricSecretKey' => 
      array (
        'name' => 'generateAsymmetricSecretKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'ParagonIE\\Paseto\\Keys\\AsymmetricSecretKey',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return AsymmetricSecretKey
 */',
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 78,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'aliasName' => NULL,
      ),
      'generateSymmetricKey' => 
      array (
        'name' => 'generateSymmetricKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return SymmetricKey
 */',
        'startLine' => 45,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 64,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'aliasName' => NULL,
      ),
      'getSymmetricKeyByteLength' => 
      array (
        'name' => 'getSymmetricKeyByteLength',
        'parameters' => 
        array (
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
        'docComment' => '/**
 * @return positive-int
 */',
        'startLine' => 50,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 60,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'aliasName' => NULL,
      ),
      'encrypt' => 
      array (
        'name' => 'encrypt',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 62,
            'endLine' => 62,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 63,
            'endLine' => 63,
            'startColumn' => 9,
            'endColumn' => 25,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'footer' => 
          array (
            'name' => 'footer',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 64,
                'endLine' => 64,
                'startTokenPos' => 157,
                'startFilePos' => 1411,
                'endTokenPos' => 157,
                'endFilePos' => 1412,
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
            'startLine' => 64,
            'endLine' => 64,
            'startColumn' => 9,
            'endColumn' => 27,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'implicit' => 
          array (
            'name' => 'implicit',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 65,
                'endLine' => 65,
                'startTokenPos' => 166,
                'startFilePos' => 1442,
                'endTokenPos' => 166,
                'endFilePos' => 1443,
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
            'startLine' => 65,
            'endLine' => 65,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
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
 * Encrypt a message using a shared key.
 *
 * @param string $data
 * @param SymmetricKey $key
 * @param string $footer
 * @param string $implicit
 * @return string
 */',
        'startLine' => 61,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 14,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'aliasName' => NULL,
      ),
      'decrypt' => 
      array (
        'name' => 'decrypt',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 78,
            'endLine' => 78,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 79,
            'endLine' => 79,
            'startColumn' => 9,
            'endColumn' => 25,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'footer' => 
          array (
            'name' => 'footer',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 80,
                'endLine' => 80,
                'startTokenPos' => 201,
                'startFilePos' => 1785,
                'endTokenPos' => 201,
                'endFilePos' => 1788,
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
                      'name' => 'string',
                      'isIdentifier' => true,
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
            'startLine' => 80,
            'endLine' => 80,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'implicit' => 
          array (
            'name' => 'implicit',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 81,
                'endLine' => 81,
                'startTokenPos' => 210,
                'startFilePos' => 1818,
                'endTokenPos' => 210,
                'endFilePos' => 1819,
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
            'startLine' => 81,
            'endLine' => 81,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
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
 * Decrypt a message using a shared key.
 *
 * @param string $data
 * @param SymmetricKey $key
 * @param string|null $footer
 * @param string $implicit
 * @return string
 */',
        'startLine' => 77,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 14,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'aliasName' => NULL,
      ),
      'sign' => 
      array (
        'name' => 'sign',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 94,
            'endLine' => 94,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'ParagonIE\\Paseto\\Keys\\AsymmetricSecretKey',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 95,
            'endLine' => 95,
            'startColumn' => 9,
            'endColumn' => 32,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'footer' => 
          array (
            'name' => 'footer',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 96,
                'endLine' => 96,
                'startTokenPos' => 245,
                'startFilePos' => 2176,
                'endTokenPos' => 245,
                'endFilePos' => 2177,
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
            'startLine' => 96,
            'endLine' => 96,
            'startColumn' => 9,
            'endColumn' => 27,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'implicit' => 
          array (
            'name' => 'implicit',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 97,
                'endLine' => 97,
                'startTokenPos' => 254,
                'startFilePos' => 2207,
                'endTokenPos' => 254,
                'endFilePos' => 2208,
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
            'startLine' => 97,
            'endLine' => 97,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
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
 * Sign a message. Public-key digital signatures.
 *
 * @param string $data
 * @param AsymmetricSecretKey $key
 * @param string $footer
 * @param string $implicit
 * @return string
 */',
        'startLine' => 93,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 14,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'aliasName' => NULL,
      ),
      'verify' => 
      array (
        'name' => 'verify',
        'parameters' => 
        array (
          'signMsg' => 
          array (
            'name' => 'signMsg',
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
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'ParagonIE\\Paseto\\Keys\\AsymmetricPublicKey',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 111,
            'endLine' => 111,
            'startColumn' => 9,
            'endColumn' => 32,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'footer' => 
          array (
            'name' => 'footer',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 112,
                'endLine' => 112,
                'startTokenPos' => 289,
                'startFilePos' => 2587,
                'endTokenPos' => 289,
                'endFilePos' => 2590,
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
                      'name' => 'string',
                      'isIdentifier' => true,
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
            'startLine' => 112,
            'endLine' => 112,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'implicit' => 
          array (
            'name' => 'implicit',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 113,
                'endLine' => 113,
                'startTokenPos' => 298,
                'startFilePos' => 2620,
                'endTokenPos' => 298,
                'endFilePos' => 2621,
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
            'startLine' => 113,
            'endLine' => 113,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
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
 * Verify a signed message. Public-key digital signatures.
 *
 * @param string $signMsg
 * @param AsymmetricPublicKey $key
 * @param string|null $footer
 * @param string $implicit
 * @return string
 */',
        'startLine' => 109,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 14,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolInterface',
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