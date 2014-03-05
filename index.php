<?php

header('Content-Type: application/json');

//////////////////////////////////////////////////
// HAVE PARAMS
//////////////////////////////////////////////////

if(isset($_GET['v']) && isset($_GET['o']) && isset($_GET['p']) && isset($_GET['w'])) {

if  (!in_array  ('curl', get_loaded_extensions())) {
        die("CURL missing");
}

    $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_URL, "https://api.github.com/repos/Codiad/Codiad/tags");
        //curl_setopt($curl, CURLOPT_POSTFIELDS, "");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13');
    
    
    $cont = curl_exec($curl);
        curl_close($curl);
        
                
    $tags = json_decode($cont, true); 
        
        
    if(isset($_GET['l'])) {
        
        $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://api.github.com/repos/Codiad/Codiad/commits");
            //curl_setopt($curl, CURLOPT_POSTFIELDS, "");
            
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13');

        $cont = curl_exec($curl);
            curl_close($curl);
        
        $commits = json_decode($cont, true); 
        $tmp = array();    
        $tmp['name'] = 'latest';
        $tmp['zipball_url'] = 'https://api.github.com/repos/Codiad/Codiad/zipball/'.$commits[0]['sha'];
        $tmp['tarball_url'] = 'https://api.github.com/repos/Codiad/Codiad/tarball/'.$commits[0]['sha'];
        $tmp['commit'] = array();
        $tmp['commit']['sha'] = $commits[0]['sha'];
        $tmp['commit']['url'] = 'https://api.github.com/repos/Codiad/Codiad/commits/'.$commits[0]['sha'];
            
        array_unshift($tags,$tmp);    
    }
    
    // translate sha into tag name
    foreach($tags as $tag) {
        if($_GET['v'] == $tag["commit"]["sha"]) {
            $_GET['v'] = $tag["name"];
            break;
        }
    }
    
    $all = json_encode($tags);
    echo($all);
    
    if(!isset($_GET['s'])) {
        // Dumping statistics for testing purposes
        file_put_contents("data/".date("Y-W").".log", $_SERVER['REMOTE_ADDR'].",".lookupGeoLocation($_SERVER['REMOTE_ADDR']).",".$_GET['p'].",".$_GET['o'].",".$_GET['v'].",".$_GET['w'].",".$_GET['a']."\r\n", FILE_APPEND | LOCK_EX);
    }
    
//////////////////////////////////////////////////
// MISSING PARAMS
//////////////////////////////////////////////////
    
} else {
    
    $err_out['error'] = "URL is missing params";
    
    echo(json_encode($err_out));
    
}

//////////////////////////////////////////////////
// COUNTRY LOOKUP
//////////////////////////////////////////////////
  
function lookupGeoLocation($ip){
    $info = file_get_contents("http://who.is/whois-ip/ip-address/$ip");
    list($a, $b) = explode('country:        ', $info);
    $country = substr($b,0,2);
    if(trim($country) == '') {
        $country = file_get_contents('http://api.hostip.info/country.php?ip='.$ip);
    }
    if(trim($country) == '' || trim($country) == 'XX') {
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
        $country = $details->country;
    }
    return $country;
}

?>
