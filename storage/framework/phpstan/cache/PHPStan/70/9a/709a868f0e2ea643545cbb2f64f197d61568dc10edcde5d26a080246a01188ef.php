<?php declare(strict_types = 1);

// osfsl-C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../paragonie/paseto/src/ProtocolCollection.php-PHPStan\BetterReflection\Reflection\ReflectionClass-ParagonIE\Paseto\ProtocolCollection
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-470ae9cf2c006b97b9ce69ea259ec109e44b56bd5a9ad5d9a3cdc927ca7d267c-8.2.12-6.70.0.6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/vendor/composer/../paragonie/paseto/src/ProtocolCollection.php',
      ),
    ),
    'namespace' => 'ParagonIE\\Paseto',
    'name' => 'ParagonIE\\Paseto\\ProtocolCollection',
    'shortName' => 'ProtocolCollection',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Class ProtocolCollection
 * @package ParagonIE\\Paseto
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 27,
    'endLine' => 210,
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
      'ALLOWED' => 
      array (
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'name' => 'ALLOWED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\\ParagonIE\\Paseto\\Protocol\\Version1::class, \\ParagonIE\\Paseto\\Protocol\\Version2::class, \\ParagonIE\\Paseto\\Protocol\\Version3::class, \\ParagonIE\\Paseto\\Protocol\\Version4::class]',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 40,
            'startTokenPos' => 95,
            'startFilePos' => 688,
            'endTokenPos' => 117,
            'endFilePos' => 794,
          ),
        ),
        'docComment' => '/**
 * Our built-in allow-list of protocol types is defined here.
 *
 * @const array<int, class-string<ProtocolInterface>>
 * @var array<int, class-string<ProtocolInterface>>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'protocols' => 
      array (
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'name' => 'protocols',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var array<array-key, ProtocolInterface> */',
        'attributes' => 
        array (
        ),
        'startLine' => 43,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'headerLookup' => 
      array (
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'name' => 'headerLookup',
        'modifiers' => 20,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 137,
            'startFilePos' => 959,
            'endTokenPos' => 138,
            'endFilePos' => 960,
          ),
        ),
        'docComment' => '/** @var array<string, ProtocolInterface> */',
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 38,
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
          'protocols' => 
          array (
            'name' => 'protocols',
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
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 33,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param ProtocolInterface ...$protocols
 *
 * @throws LogicException
 * @throws InvalidVersionException
 */',
        'startLine' => 54,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'aliasName' => NULL,
      ),
      'has' => 
      array (
        'name' => 'has',
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
            'startLine' => 76,
            'endLine' => 76,
            'startColumn' => 25,
            'endColumn' => 51,
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
 * Does the collection contain the given protocol
 * @param ProtocolInterface $protocol
 *
 * @return bool
 */',
        'startLine' => 76,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'aliasName' => NULL,
      ),
      'isValid' => 
      array (
        'name' => 'isValid',
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
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 36,
            'endColumn' => 62,
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
 * Is the given protocol supported?
 *
 * @param ProtocolInterface $protocol
 * @return bool
 */',
        'startLine' => 87,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'aliasName' => NULL,
      ),
      'throwIfUnsupported' => 
      array (
        'name' => 'throwIfUnsupported',
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
            'startLine' => 100,
            'endLine' => 100,
            'startColumn' => 47,
            'endColumn' => 73,
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
 * Throws if the given protocol is unsupported
 *
 * @param ProtocolInterface $protocol
 * @return void
 *
 * @throws InvalidVersionException
 */',
        'startLine' => 100,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'aliasName' => NULL,
      ),
      'protocolFromHeaderPart' => 
      array (
        'name' => 'protocolFromHeaderPart',
        'parameters' => 
        array (
          'headerPart' => 
          array (
            'name' => 'headerPart',
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
            'startLine' => 118,
            'endLine' => 118,
            'startColumn' => 51,
            'endColumn' => 68,
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
            'name' => 'ParagonIE\\Paseto\\ProtocolInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return the PASETO protocol version for a given header snippet
 *
 * @param string $headerPart
 *
 * @return ProtocolInterface
 * @throws InvalidVersionException
 */',
        'startLine' => 118,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'aliasName' => NULL,
      ),
      'default' => 
      array (
        'name' => 'default',
        'parameters' => 
        array (
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
 * Get a collection of all supported protocols
 *
 * @return self
 *
 * @throws InvalidVersionException
 */',
        'startLine' => 148,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'aliasName' => NULL,
      ),
      'v1' => 
      array (
        'name' => 'v1',
        'parameters' => 
        array (
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
 * Get a collection containing protocol version 1.
 *
 * @return self
 *
 * @throws InvalidVersionException
 * @throws SecurityException
 *
 * @deprecated See Version3 instead.
 */',
        'startLine' => 168,
        'endLine' => 171,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'aliasName' => NULL,
      ),
      'v2' => 
      array (
        'name' => 'v2',
        'parameters' => 
        array (
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
 * Get a collection containing protocol version 2.
 *
 * @return self
 *
 * @throws InvalidVersionException
 *
 * @deprecated See Version4 instead.
 */',
        'startLine' => 182,
        'endLine' => 185,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'aliasName' => NULL,
      ),
      'v3' => 
      array (
        'name' => 'v3',
        'parameters' => 
        array (
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
 * Get a collection containing protocol version 3.
 *
 * @return self
 *
 * @throws InvalidVersionException
 */',
        'startLine' => 194,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'aliasName' => NULL,
      ),
      'v4' => 
      array (
        'name' => 'v4',
        'parameters' => 
        array (
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
 * Get a collection containing protocol version 4.
 *
 * @return self
 *
 * @throws InvalidVersionException
 */',
        'startLine' => 206,
        'endLine' => 209,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'ParagonIE\\Paseto',
        'declaringClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'implementingClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
        'currentClassName' => 'ParagonIE\\Paseto\\ProtocolCollection',
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