ey = $ec->keyFromSecret('61233ca4590acd'); // hex string or array of bytes

// Sign message (can be hex sequence or array)
$msg = 'ab4c3451';
$signature = $key->sign($msg)->toHex();

// Verify signature
echo "Verified: " . (($key->verify($msg, $signature) == TRUE) ? "true" : "false") . "\n";

// CHECK WITH NO PRIVATE KEY

// Import public key
$pub = '2763d01c334250d3e2dda459e5e3f949f667c6bbf0a35012c77ad40b00f0374d';
$key = $ec->keyFromPublic($pub, 'hex');

// Verify signature
$signature = '93899915C2919181A3D244AAAC032CE78EF76D2FFC0355D4BE2C70F48202EBC5F2BB0541D236182F55B11AC6346B524150695E5DE1FEA570786E1CC1F7999404';
echo "Verified: " . (($key->verify($msg, $signature) == TRUE) ? "true" : "false") . "\n";
```

### ECDH

```php
<?php
use Elliptic\EC;

$ec = new EC('curve25519');

// Generate keys
$key1 = $ec->genKeyPair();
$key2 = $ec->genKeyPair();

$shared1 = $key1->derive($key2->getPublic());
$shared2 = $key2->derive($key1->getPublic());

echo "Both shared secrets are BN instances\n";
echo $shared1->toString(16) . "\n";
echo $shared2->toString(16) . "\n";
```

NOTE: `.derive()` returns a [BN][1] instance.

### Using EC directly

Use case examples:

#### Computing public key from private 

```php
use Elliptic\EC;

$ec = new EC('secp256k1');

$priv_hex = "751ce088f64404e5889bf7e9e5c280b200b2dc158461e96b921df39a1dbc6635";
$pub_hex  = "03a319a1d10a91ada9a01ab121b81ae5f14580083a976e74945cdb014a4a52bae6";

$priv = $ec->keyFromPrivate($priv_hex);
if ($pub_hex == $priv->getPublic(true, "hex")) {
    echo "Success\n";
} else {
    echo "Fail\n";
}
```

#### Verifying Bitcoin Message Signature

```php
use Elliptic\EC;
use StephenHill\Base58;

// see: https://en.bitcoin.it/wiki/List_of_address_prefixes
const MainNetId = "\x00";
const TestNetId = "\x6F";
const PrefixNetIdMap = [ "1" => MainNetId, "m" => 