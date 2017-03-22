<?php
//Download images from pexels.com
$keyword = "flower garden"; //the keyword you want to use for searching images in pexels.com website.
$final_keyword = str_replace(" ", "%20", $keyword);//replacing the space with the format used by the website.
$url = "https://www.pexels.com/search/$final_keyword/";//url of the website.

$output = get($url);
//echo $output;
function get($url){
	//adding headers for curl in an array
    $headers = Array(
                    "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
                    "Cache-Control: max-age=0",
                    "Connection: keep-alive",
                    "Keep-Alive: 300",
                    "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
                    "Accept-Language: en-us,en;q=0.5",
                    "Pragma: "
                );
		//adding configs for curl in an array
        $config = Array(
                        CURLOPT_SSL_VERIFYPEER => true,
                        CURLOPT_SSL_VERIFYHOST => 2,
                        CURLOPT_CAINFO => 'D:\xampp\htdocs\scrape\CAcert.pem', //add the certificate (CAcert.pem) path
                        CURLOPT_RETURNTRANSFER => TRUE ,
                        CURLOPT_FOLLOWLOCATION => TRUE ,
                        CURLOPT_AUTOREFERER => TRUE ,
                        CURLOPT_CONNECTTIMEOUT => 120 ,
                        CURLOPT_TIMEOUT => 120 ,
                        CURLOPT_MAXREDIRS => 10 ,                   
                        CURLOPT_USERAGENT => "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8" ,
                        CURLOPT_URL => $url //the url of website
                       ) ;
    
    $handle = curl_init();//initialize curl
    curl_setopt_array($handle,$config);//adding config
    curl_setopt($handle,CURLOPT_HTTPHEADER,$headers); //adding headers

    $output = curl_exec($handle); //storing curl result string in variable.
    curl_close($handle);//close curl
    return $output; //return the result string of the the function
}
//find image url from the result string using regular expression and store in the array $url_matches
preg_match_all('!data-pin-media="(.*?)\?!', $output, $url_matches);
//print_r($url_matches[1]);

$local_path = "D:\img\\"; //replace the path where you want to download the images. Make sure the folder is there and writable.

//Download all images by looping through the urls
for($i=0; $i<count($url_matches[1]); $i ++){//loop through the array $url_matches
    //match all of the content with forward slash
    preg_match_all("!.*?/!", $url_matches[1][$i], $matches); //find part of the url to get the image name
    //print_r($matches[0]); die();
    $last_part = end($matches[0]); //get the last part of the url parts array
    //print_r($last_part);die();
    preg_match("!$last_part(.*?.jpg|.*?.jpeg)!", $url_matches[1][$i], $match); //find the image name using the url part and regular expression.
    //print_r($match);
    $image_name = str_replace("+", "-", $match[1]); //replace the '+' sign with '-'
    //echo $image_name;die();
    //save image url in a variable
    $image_url = $url_matches[1][$i];
    $image_data = get($image_url); //get the image data from the image url
    
    $file =fopen($local_path.$image_name, 'w'); //open a file to write the image.
    fwrite($file, $image_data); //write the image data to the file
    fclose($file); //close the file
}