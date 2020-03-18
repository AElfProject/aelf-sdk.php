<?php  
namespace Aelf\Bytes;
/**
 * * byte array and string conversion class
*/
class Bytes {


    /* *



    * converts a String String to a byte array



    * @param $STR requires the string to be converted



    * @param $bytes target byte array



    * @ author Zikie



    */
    public static function getBytes($string) {
        $bytes = array();
        for($i = 0; $i < strlen($string); $i++){
            
             $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }
    /**
    * string to hexadecimal function
    * @ pream string $STR = 'ABC';
    */
	public static function strToHex($str){
		$hex="";
		for($i=0;$i<strlen($str);$i++)
		$hex.=dechex(ord($str[$i]));
		$hex=strtoupper($hex);
		return $hex;
	}

	/**
	*
	*@pream string $hex='616263';
	*/
	public static function hexToStr($hex){
		$str="";
		for($i=0;$i<strlen($hex)-1;$i+=2)
		$str.=chr(hexdec($hex[$i].$hex[$i+1]));
		return  $str;
	}

    /**
    *converts a byte array to data of type String
    *@param $bytes array
    *@param $STR target string
    *@return a String of data
    */
    public static function toStr($bytes) {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }
           return $str;
    }


    /**
    * Converts an int to a byte array
    *@param $byt target byte array
    *@param $val string to be converted
    *
    */
    public static function integerToBytes($val) {
        $byt = array();
        $byt[0] = ($val & 0xff);
        $byt[1] = ($val >> 8 & 0xff);
        $byt[2] = ($val >> 16 & 0xff);
        $byt[3] = ($val >> 24 & 0xff);
        return $byt;
    }

    /**
    * reads an Integer from the specified position in the byte array
    * @param $bytes array
    * @param $position the starting position specified
    * @return an Integer type of data
    */
    public static function bytesToInteger($bytes, $position) {
        $val = 0;
        $val = $bytes[$position + 3] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 2] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 1] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position] & 0xff;
        return $val;
    }

    /**
    * converts a shor string to a byte array
    * @param $byt target byte array
    * @param $val string to be converted
    *
    */
    public static function shortToBytes($val) {
        $byt = array();
        $byt[0] = ($val & 0xff);
        $byt[1] = ($val >> 8 & 0xff);
        return $byt;
    }


    /**
    * reads data of type Short from the specified location in the byte array.
    * @param $bytes array
    * @param $position the starting position specified
    * @return a Short type of data
    */
    public static function bytesToShort($bytes, $position) {  
        $val = 0;  
        $val = $bytes[$position + 1] & 0xFF;  
        $val = $val << 8;  
        $val |= $bytes[$position] & 0xFF;  
        return $val;  
    }  
   
}  
?>