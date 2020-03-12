
# Fast Elliptic Curve Cryptography in PHP


## Information

This library is a PHP port of [elliptic](https://github.com/indutny/elliptic), a great JavaScript ECC library.

* Supported curve types: Short Weierstrass, Montgomery, Edwards, Twisted Edwards.
* Curve 'presets': `secp256k1`, `p192`, `p224`, `p256`, `p384`, `p521`, `curve25519`, `ed25519`.

This software is licensed under the MIT License.

Projects which use Fast ECC PHP library: [PrivMX WebMail](https://privmx.com), ...


## Benchmarks

```
+------------------------+----------------+--------+-----+------+
| subject                | mode           | rstdev | its | revs |
+------------------------+----------------+--------+-----+------+
| elliptic#genKeyPair    | 323.682ops/s   | 2.72%  | 5   | 50   |
| mdanter#genKeyPair     | 13.794ops/s    | 3.18%  | 5   | 50   |
+------------------------+----------------+--------+-----+------+
| elliptic#sign          | 307.228ops/s   | 3.82%  | 5   | 50   |
| mdanter#sign           | 14.118ops/s    | 2.12%  | 5   | 50   |
+------------------------+----------------+--------+-----+------+
| elliptic#verify        | 93.913ops/s    | 5.93%  | 5   | 50   |
| mdanter#verify         | 6.859ops/s     | 2.95%  | 5   | 50   |
+------------------------+----------------+--------+-----+------+
| elliptic#dh            | 135.166ops/s   | 1.67%  | 5   | 50   |
| mdanter#dh             | 14.302ops/s    | 0.89%  | 5   | 50   |
+------------------------+----------------+--------+-----+------+
| elliptic#EdDSASign     | 296.756ops/s   | 1.09%  | 5   | 50   |
+------------------------+----------------+--------+-----+------+
| elliptic#EdDSAVerify   | 67.481ops/s    | 2.76%  | 5   | 50   |
+------------------------+----------------+--------+-----+------+
```


## Installation

You can install this library via Composer:
```
composer require simplito/elliptic-php
```


## Implementation details

ECDSA is using deterministic `k` value generation as per [RFC6979][0]. Most of
the curve operations are performed on non-affine coordinates (either projective
or extended), various windowing techniques are used for different cases.

NOTE: `curve25519` could not be used for ECDSA, use `ed25519` instead.

All operations are performed in reduction context using [bn-php][1].


## API

### ECDSA

```php
<?php
use Elliptic\EC;

// Create and initialize EC context
// (better do it once and reuse it)
$ec = new EC('secp256k1');

// Generate keys
$key = $ec->genKeyPair();

// Sign message (can be hex sequence or array)
$msg = 'ab4c3451';
$signature = $key->sign($msg);

// Export DER encoded signature to hex string
$derSign = $signature->toDER('hex');

// Verify signature
echo "Verified: " . (($key->verify($msg, $derSign) == TRUE) ? "true" : "false") . "\n";

// CHECK WITH NO PRIVATE KEY

// Public key as '04 + x + y'
$pub = "049a1eedae838f2f8ad94597dc4368899ecc751342b464862da80c280d841875ab4607fb6ce14100e71dd7648