<?php
$keyword = "flower garden";
$final_keyword = str_replace(" ", "%20", $keyword);
$url = "https://www.pexels.com/search/$final_keyword/";

$output = get($url);
//echo $output;
function get($url){
    $headers = Array(
                    "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
                    "Cache-Control: max-age=0",
                    "Connection: keep-alive",
                    "Keep-Alive: 300",
                    "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
                    "Accept-Language: en-us,en;q=0.5",
                    "Pragma: "
                );
        $config = Array(
                        CURLOPT_SSL_VERIFYPEER => true,
                        CURLOPT_SSL_VERIFYHOST => 2,
                        CURLOPT_CAINFO => 'D:\xampp\htdocs\scrape\CAcert.pem',
                        CURLOPT_RETURNTRANSFER => TRUE ,
                        CURLOPT_FOLLOWLOCATION => TRUE ,
                        CURLOPT_AUTOREFERER => TRUE ,
                        CURLOPT_CONNECTTIMEOUT => 120 ,
                        CURLOPT_TIMEOUT => 120 ,
                        CURLOPT_MAXREDIRS => 10 ,                   
                        CURLOPT_USERAGENT => "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8" ,
                        CURLOPT_URL => $url
                       ) ;
    
    
    $handle = curl_init();
    curl_setopt_array($handle,$config) ;
    curl_setopt($handle,CURLOPT_HTTPHEADER,$headers) ;
    //curl_setopt($handle, CURLOPT_URL, $url);
    //curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($handle);
    curl_close($handle);
    return $output;
}

preg_match_all('!data-pin-media="(.*?)\?!', $output, $url_matches);
//print_r($url_matches[1]);

$local_path = "D:\img\\";
for($i=0; $i<count($url_matches[1]); $i ++){
    //match all of the content with forward slash
    preg_match_all("!.*?/!", $url_matches[1][$i], $matches);
    //print_r($matches[0]); die();
    $last_part = end($matches[0]);
    //print_r($last_part);die();
    preg_match("!$last_part(.*?.jpg|.*?.jpeg)!", $url_matches[1][$i], $match);
    //print_r($match);
    $image_name = str_replace("+", "-", $match[1]);
    //echo $image_name;die();
    //save image url in a variable
    $image_url = $url_matches[1][$i];
    $image_data = get($image_url);
    
    $file =fopen($local_path.$image_name, 'w');
    fwrite($file, $image_data);
    fclose($file);
}