{
  "name": "bitwasp/bitcoin",
  "description": "PHP Bitcoin library with functions for transactions, signatures, serialization, Random/Deterministic ECDSA keys, blocks, RPC bindings",
  "type": "library",
  "homepage": "https://github.com/bit-wasp/bitcoin-php",
  "license": "Unlicense",
  "authors": [
    {
      "name": "Thomas Kerin",
      "homepage": "https://thomaskerin.io",
      "role": "Author"
    }
  ],
  "autoload": {
    "psr-4": {
      "BitWasp\\Bitcoin\\": "src/"
    },
    "files": ["src/Script/functions.php"]
  },
  "autoload-dev": {
    "psr-4": {
      "BitWasp\\Bitcoin\\Tests\\": "tests/"
    }
  },
  "require": {
    "php-64bit": ">=7.0",
    "pleonasm/merkle-tree": "1.0.0",
    "compo