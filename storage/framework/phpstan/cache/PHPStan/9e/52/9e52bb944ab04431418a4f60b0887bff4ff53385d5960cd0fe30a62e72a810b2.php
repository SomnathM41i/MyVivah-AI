<?php declare(strict_types = 1);

// osfsl-C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../paragonie/paseto/src/Keys/SymmetricKey.php-PHPStan\BetterReflection\Reflection\ReflectionClass-ParagonIE\Paseto\Keys\SymmetricKey
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-480ce577c039849ef71b3e9ab12e60d9fb64413f60405355a97ebc8caa2c3697-8.2.12-6.70.0.6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../paragonie/paseto/src/Keys/SymmetricKey.php',
      ),
    ),
    'namespace' => 'ParagonIE\\Paseto\\Keys',
    'name' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
    'shortName' => 'SymmetricKey',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Class SymmetricKey
 * @package ParagonIE\\Paseto\\Keys
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 31,
    'endLine' => 296,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'ParagonIE\\Paseto\\ReceivingKey',
      1 => 'ParagonIE\\Paseto\\SendingKey',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'INFO_ENCRYPTION' => 
      array (
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'name' => 'INFO_ENCRYPTION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'paseto-encryption-key\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 109,
            'startFilePos' => 602,
            'endTokenPos' => 109,
            'endFilePos' => 624,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'INFO_AUTHENTICATION' => 
      array (
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'name' => 'INFO_AUTHENTICATION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'paseto-auth-key-for-aead\'',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 118,
            'startFilePos' => 659,
            'endTokenPos' => 118,
            'endFilePos' => 684,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
    ),
    'immediateProperties' => 
    array (
      'key' => 
      array (
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'name' => 'key',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 129,
            'startFilePos' => 737,
            'endTokenPos' => 129,
            'endFilePos' => 738,
          ),
        ),
        'docComment' => '/** @var string $key */',
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'protocol' => 
      array (
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'name' => 'protocol',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var ProtocolInterface $protocol */',
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 24,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'keyMaterial' => 
          array (
            'name' => 'keyMaterial',
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
            'startLine' => 49,
            'endLine' => 49,
            'startColumn' => 9,
            'endColumn' => 27,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'protocol' => 
          array (
            'name' => 'protocol',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 50,
                'endLine' => 50,
                'startTokenPos' => 159,
                'startFilePos' => 1049,
                'endTokenPos' => 159,
                'endFilePos' => 1052,
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
                      'name' => 'ParagonIE\\Paseto\\ProtocolInterface',
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
            'startLine' => 50,
            'endLine' => 50,
            'startColumn' => 9,
            'endColumn' => 42,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * SymmetricKey constructor.
 *
 * @param string $keyMaterial
 * @param ProtocolInterface|null $protocol
 */',
        'startLine' => 48,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      '__destruct' => 
      array (
        'name' => '__destruct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Wipe secrets before freeing memory
 */',
        'startLine' => 59,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'generate' => 
      array (
        'name' => 'generate',
        'parameters' => 
        array (
          'protocol' => 
          array (
            'name' => 'protocol',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 70,
                'endLine' => 70,
                'startTokenPos' => 231,
                'startFilePos' => 1482,
                'endTokenPos' => 231,
                'endFilePos' => 1485,
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
                      'name' => 'ParagonIE\\Paseto\\ProtocolInterface',
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
            'startLine' => 70,
            'endLine' => 70,
            'startColumn' => 37,
            'endColumn' => 70,
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
 * @param ProtocolInterface|null $protocol
 * @return SymmetricKey
 *
 * @throws Exception
 */',
        'startLine' => 70,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'v1' => 
      array (
        'name' => 'v1',
        'parameters' => 
        array (
          'keyMaterial' => 
          array (
            'name' => 'keyMaterial',
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
            'startLine' => 91,
            'endLine' => 91,
            'startColumn' => 31,
            'endColumn' => 49,
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
 * Initialize a v1 symmetric key.
 *
 * @param string $keyMaterial
 *
 * @return self
 *
 * @throws Exception
 * @throws TypeError
 *
 * @deprecated See Version3 instead.
 */',
        'startLine' => 91,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'v2' => 
      array (
        'name' => 'v2',
        'parameters' => 
        array (
          'keyMaterial' => 
          array (
            'name' => 'keyMaterial',
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
            'startLine' => 108,
            'endLine' => 108,
            'startColumn' => 31,
            'endColumn' => 49,
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
 * Initialize a v2 symmetric key.
 *
 * @param string $keyMaterial
 *
 * @return self
 *
 * @throws Exception
 * @throws TypeError
 *
 * @deprecated See Version4 instead.
 */',
        'startLine' => 108,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'v3' => 
      array (
        'name' => 'v3',
        'parameters' => 
        array (
          'keyMaterial' => 
          array (
            'name' => 'keyMaterial',
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
            'startLine' => 123,
            'endLine' => 123,
            'startColumn' => 31,
            'endColumn' => 49,
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
 * Initialize a v3 symmetric key.
 *
 * @param string $keyMaterial
 *
 * @return self
 *
 * @throws Exception
 * @throws TypeError
 */',
        'startLine' => 123,
        'endLine' => 126,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'v4' => 
      array (
        'name' => 'v4',
        'parameters' => 
        array (
          'keyMaterial' => 
          array (
            'name' => 'keyMaterial',
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
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 31,
            'endColumn' => 49,
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
 * Initialize a v4 symmetric key.
 *
 * @param string $keyMaterial
 *
 * @return self
 *
 * @throws Exception
 * @throws TypeError
 */',
        'startLine' => 138,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'encode' => 
      array (
        'name' => 'encode',
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
 * Return a base64url-encoded representation of this symmetric key.
 *
 * @return string
 *
 * @throws TypeError
 */',
        'startLine' => 150,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'fromEncodedString' => 
      array (
        'name' => 'fromEncodedString',
        'parameters' => 
        array (
          'encoded' => 
          array (
            'name' => 'encoded',
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
            'startLine' => 164,
            'endLine' => 164,
            'startColumn' => 46,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'version' => 
          array (
            'name' => 'version',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 164,
                'endLine' => 164,
                'startTokenPos' => 482,
                'startFilePos' => 3558,
                'endTokenPos' => 482,
                'endFilePos' => 3561,
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
                      'name' => 'ParagonIE\\Paseto\\ProtocolInterface',
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
            'startLine' => 164,
            'endLine' => 164,
            'startColumn' => 63,
            'endColumn' => 95,
            'parameterIndex' => 1,
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
 * Initialize a symmetric key from a base64url-encoded string.
 *
 * @param string $encoded
 * @param ProtocolInterface|null $version
 * @return self
 *
 * @throws TypeError
 */',
        'startLine' => 164,
        'endLine' => 168,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'getProtocol' => 
      array (
        'name' => 'getProtocol',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'ParagonIE\\Paseto\\ProtocolInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the version of PASETO that this key is intended for.
 *
 * @return ProtocolInterface
 */',
        'startLine' => 175,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'isForVersion' => 
      array (
        'name' => 'isForVersion',
        'parameters' => 
        array (
          'protocol' => 
          array (
            'name' => 'protocol',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'ParagonIE\\Paseto\\ProtocolInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 184,
            'endLine' => 184,
            'startColumn' => 34,
            'endColumn' => 60,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param ProtocolInterface $protocol
 * @return bool
 */',
        'startLine' => 184,
        'endLine' => 187,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'raw' => 
      array (
        'name' => 'raw',
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
 * Get the raw key contents.
 *
 * @return string
 */',
        'startLine' => 194,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'splitV3' => 
      array (
        'name' => 'splitV3',
        'parameters' => 
        array (
          'salt' => 
          array (
            'name' => 'salt',
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
            'startLine' => 211,
            'endLine' => 211,
            'startColumn' => 29,
            'endColumn' => 40,
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
 * Split this key into two 256-bit keys and a nonce, using HKDF-SHA384
 * (with the given salt)
 *
 * Used in version 3
 *
 * @param string $salt
 * @return array<int, string>
 *
 * @throws PasetoException
 * @throws TypeError
 */',
        'startLine' => 211,
        'endLine' => 228,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'splitV4' => 
      array (
        'name' => 'splitV4',
        'parameters' => 
        array (
          'salt' => 
          array (
            'name' => 'salt',
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
            'startLine' => 242,
            'endLine' => 242,
            'startColumn' => 29,
            'endColumn' => 40,
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
 * Split this key into two 256-bit keys and a nonce, using BLAKE2b-MAC
 * (with the given salt)
 *
 * Used in version 4
 *
 * @param string $salt
 * @return array<int, string>
 *
 * @throws SodiumException
 */',
        'startLine' => 242,
        'endLine' => 256,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      'split' => 
      array (
        'name' => 'split',
        'parameters' => 
        array (
          'salt' => 
          array (
            'name' => 'salt',
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
            'startLine' => 270,
            'endLine' => 270,
            'startColumn' => 27,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Split this key into two 256-bit keys, using HKDF-SHA384
 * (with the given salt)
 *
 * Used in versions 1 and 2
 *
 * @param string $salt
 * @return array<int, string>
 *
 * @throws PasetoException
 * @throws TypeError
 */',
        'startLine' => 270,
        'endLine' => 287,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'aliasName' => NULL,
      ),
      '__debugInfo' => 
      array (
        'name' => '__debugInfo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array
 */',
        'startLine' => 292,
        'endLine' => 295,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto\\Keys',
        'declaringClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'implementingClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
        'currentClassName' => 'ParagonIE\\Paseto\\Keys\\SymmetricKey',
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