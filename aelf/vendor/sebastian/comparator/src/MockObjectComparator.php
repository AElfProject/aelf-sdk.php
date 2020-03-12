 }

    return "{$hrp}1" . implode('', $encoded);
}

/**
 * @throws Bech32Exception
 * @param string $sBech - the bech32 encoded string
 * @return array - returns [$hrp, $dataChars]
 */
function decodeRaw($sBech)
{
    $length = strlen($sBech);
    if ($length < 8) {
        throw new Bech32Exception("Bech32 string is too short");
    }

    $chars = array_values(unpack('C*', $sBech));

    $haveUpper = false;
    $haveLower = false;
    $positionOne = -1;

    for ($i = 0; $i < $length; $i++) {
        $x = $chars[$i];
        if ($x < 33 || $x > 126) {
            throw new Bech32Exception('Out of range character in bech32 string');
        }

        if ($x >= 0x61 && $x <= 0x7a) {
            $haveLower = true;
        }

        if ($x >= 0x41 && $x <= 0x5a) {
            $haveUpper = true;
            $x = $chars[$i] = $x + 0x20;
        }

        // find location of last '1' character
        if ($x === 0x31) {
            $positionOne = $i;
        }
    }

    if ($haveUpper && $haveLower) {
        throw new Bech32Exception('Data contains mixture of higher/lower case characters');
    }

    if ($positionOne === -1) {
        throw new Bech32Exception("Missing separator character");
    }

    if ($positionOne < 1) {
        throw new Bech32Exception("Empty HRP");
    }

    if (($positionOne + 7) > $length) {
        throw new 