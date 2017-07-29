<?php
function updateShortURL($url){
    $shortenURL_body=array("longUrl"=>$url);
    $ch = curl_init(SHORT_URL.'?fields=id%2ClongUrl%2Cstatus&key='.SHORTEN_URL_API_KEY);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        CURLOPT_POSTFIELDS => json_encode($shortenURL_body)
    ));
    // Send the request
    $response = curl_exec($ch);
    // Check for errors
    if($response === FALSE){
        return false;    
        // die(curl_error($ch));
    }
    // Decode the response
    $shortenURL_response= json_decode($response, TRUE);
    //return $shortenURL_response['id'];
    echo json_encode($shortenURL_response);
    
}

function getLongURL($url){
    $encoded_URL=urlencode($url);
    $longURL_response=file_get_contents(SHORT_URL."?shortUrl=".$encoded_URL."&key=".SHORTEN_URL_API_KEY);
    $longURL_response=json_decode($longURL_response,TRUE);
    if(!$longURL_response){
        return false;
    }
    return $longURL_response['longUrl'];
}
?>