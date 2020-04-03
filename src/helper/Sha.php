<?php
use StephenHill\Base58;
/*

Currently (PHP 7.1) has no built-in function to calculate, sha1() sha1_file() md5() md5_file() can be used to calculate the sha1 hash value and md5 hash value of a string and a file, respectively, nor does the current version of PHP 7.1 sha256() sha256_file() sha512() sha512_file(). Sha-2 is the combined name of sha-224, sha-256, sha-384, and sha-512.

PHP computes sha256 sha512 using the hash() function, and computes sha256 sha512 using hash_file().

Hash ($algo, $data, $rawOutput);

Hash_file ($algo, $filepath, $rawOutput);

Where $algo is the algorithm, which can be the sha256, sha512 equivalent, and the supported algorithm can be viewed using hash_algos(), which returns all the supported algorithms.

$data is the string that needs to compute the hash value, and $filepath is the file name that needs to compute the hash value, either relative or absolute.

$rawOutput is an optional Boolean parameter that returns binary data if true, or a string if false, with a default value of false.

We can encapsulate custom functions that allow PHP to compute sha256 sha512 and other types of hash values.

The following code implements PHP sha256() sha256_file() sha512() sha512_file()

*/


/*

* the following code makes PHP sha256() sha256_file() sha512() sha512_file() PHP 5.1.2+ perfectly compatible

* @param string $data is the string that evaluates the hash value

* @param Boolean $rawOutput returns raw binary data when true, otherwise returns a string

* @param string file is the file name for which the hash value is to be calculated, either as a separate file name or as a path, either absolute or relative

* @return Boolean | string returns false if the parameter is invalid or the file does not exist or the file is unreadable, and the corresponding hash value is returned on success

* @notes using example sha256('www.wuxiancheng.cn') sha512('www.wuxiancheng.cn') sha256_file('index.php') sha512_file('index.php')

* /

PHP / * sha256 ()*/
function sha256($data, $rawOutput = false)
{
    if (!is_scalar($data)) {
        return false;
    }
    $data = (string)$data;
    $rawOutput = !!$rawOutput;
    return hash('sha256', $data, $rawOutput);
}
/* PHP sha256_file() */
function sha256_file($file, $rawOutput = false)
{
    if (!is_scalar($file)) {
        return false;
    }
    $file = (string)$file;
    if (!is_file($file) || !is_readable($file)) {
        return false;
    }
    $rawOutput = !!$rawOutput;
    return hash_file('sha256', $file, $rawOutput);
}
function decodeChecked($address)
{
    $base58 = new base58();
    $address   = $base58->decode($address);
    if(strlen($address) < 4)
        return false;
    $checksum   = substr($address, strlen($address)-4, 4);
  
    $rawAddress = substr($address, 0, strlen($address)-4);

    if(substr(hex2bin(Sha256twice($rawAddress)), 0, 4) === $checksum)
        return $rawAddress;
    else
        return false;
}

function encodeChecked($address){
    $base58 = new base58();
    $checksum =hex2bin(Sha256twice($address));
    $address = $address.substr($checksum, 0, 4);
    $address = $base58->encode($address);
    return $address;
}
function checksum($input){
    return hash('sha256', hex2bin(hash('sha256', $input)));
}


function SHA256Hex($str){
    $re=hash('sha256', $str, true);
    return bin2hex($re);
}
function Sha256twice($str){
 
   return hash('sha256', hex2bin(hash('sha256', $str)));
}
/* PHP sha512() */
function sha512($data, $rawOutput = false)
{
    if (!is_scalar($data)) {
        return false;
    }
    $data = (string)$data;
    $rawOutput = !!$rawOutput;
    return hash('sha512', $data, $rawOutput);
}

/* PHP sha512_file()*/
function sha512_file($file, $rawOutput = false)
{
    if (!is_scalar($file)) {
        return false;
    }
    $file = (string)$file;
    if (!is_file($file) || !is_readable($file)) {
        return false;
    }
    $rawOutput = !!$rawOutput;
    return hash_file('sha512', $file, $rawOutput);
}
?>