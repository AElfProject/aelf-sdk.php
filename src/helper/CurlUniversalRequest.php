<?php
/**
*send an HTTP request
*@param string $url request address
*@param string $method HTTP method (GET POST PUT DELETE)
*@param array $data HTTP request data
*@param array $header HTTP request header
*@param Int $type requests data type 0-array 1- Jason
* @ return string | bool
 */
function send_request($url, $method = "POST", $data = array(), $header = array(), $type = '0') {
    //检查地址是否为空
    if (empty($url)) {
        return false;
    }
    //控制请求方法范围
    $httpMethod = array('GET', 'POST', 'PUT', 'DELETE');
    $method = strtoupper($method);
    if (!in_array($method, $httpMethod)) {
        return false;
    }
    //请求头初始化
    $request_headers = array();
    $User_Agent = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31';
    $request_headers[] = 'User-Agent: '. $User_Agent;
    if($header){
        foreach ($header as $v) {
            $request_headers[] = $v;
        }
    }

    $request_headers[] = 'Accept: text/html,application/json,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    switch ($method) {
        case "POST":
            $request_headers[] = "X-HTTP-Method-Override: POST";
            break;
        case "PUT":
            $request_headers[] = "X-HTTP-Method-Override: PUT";
            break;
        case "DELETE":
            $request_headers[] = "X-HTTP-Method-Override: DELETE";
            break;
        default:
    }
    //发送http请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//https
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
    switch ($method) {
        case "POST":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            break;
        case "PUT":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            break;
        case "DELETE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
        default:
    }

    //格式化发送数据
    if($data) {
        if ($type) {
            $dataValue = json_encode($data,JSON_UNESCAPED_UNICODE);
        }else{
            $dataValue = http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataValue);
    }

    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    //发送请求获取返回响应
    $result['data'] = curl_exec($ch);
    $result['httpCode'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    if(strlen(curl_error($ch))>1){
        $result = false;
    }

    curl_close($ch);
    return $result;
}

?>