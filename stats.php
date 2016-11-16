<?php

    //header('Content-Type: application/json');

    $data = getcwd().'/data';
    $ip = array();
    $country = array();
    $php = array();
    $os = array();
    $version = array();
    $webserver = array();
    $week = array();

    foreach (scandir($data) as $name){
        if($name == '.' || $name == '..' || $name == '.htaccess'){
            continue;
        }
        if(is_file($data.'/'.$name)){
            $file = fopen($data.'/'.$name,'r');
            while(!feof($file)) { 
                $line = explode(",",fgets($file));
                $hash = hash("crc32", $line[0]);

                if(!array_key_exists($hash,$ip)) {
                    //array_push($ip,$line[0]);
                    $ip[$hash] = trim($line[4]);
                    
                    if(!array_key_exists(substr($name,0,-4),$week)) {
                        $week[substr($name,0,-4)] = 1;
                    } else {
                        $week[substr($name,0,-4)]++;
                    }
                    
                    if(!array_key_exists(strtoupper($line[1]),$country)) {
                        $country[strtoupper($line[1])] = 1;
                    } else {
                        $country[strtoupper($line[1])]++;
                    }
    
                    $line[2] = substr($line[2],0,3);
                    if(!array_key_exists(strtoupper($line[2]),$php)) {
                        $php[strtoupper($line[2])] = 1;
                    } else {
                        $php[strtoupper($line[2])]++;
                    }
                    
                    if(!array_key_exists(strtoupper($line[3]),$os)) {
                        $os[strtoupper($line[3])] = 1;
                    } else {
                        $os[strtoupper($line[3])]++;
                    }
                                        
                    $line[5] = explode("/", $line[5]);
                    $line[5] = $line[5][0]; //." ".substr($line[5][1],0,3);
                    if(!array_key_exists(strtoupper($line[5]),$webserver)) {
                        $webserver[addslashes(strtoupper($line[5]))] = 1;
                    } else {
                        $webserver[addslashes(strtoupper($line[5]))]++;
                    }
                    
                } else {
                    $search  = array('v', 'V', '.');
                    $replace = array('', '', '');    
                    if($ip[$hash] == '' || ( $line[4] != '' && str_replace($search, $replace, $line[4]) > str_replace($search, $replace, $ip[$hash]))) {
                        $ip[$hash] = trim($line[4]);
                    }
                }
            }
            fclose($file);
        }
    }
    
    foreach($ip as $key=>$value) {
        if(!array_key_exists(strtoupper($value),$version)) {
            $version[strtoupper($value)] = 1;
        } else {
            $version[strtoupper($value)]++;
        }
    }
    
    /*echo '[{';
    echo '"installations":"'.sizeof($ip).'",';
    echo '"countries":['.json_encode($country).'],';
    echo '"php":['.json_encode($php).'],';
    echo '"os":['.json_encode($os).'],';
    echo '"webserver":['.json_encode($webserver).']';
    echo '}]';*/
    
    $data = array();
    $data["Installation_Country"] = $country;
    $data["Installation_Week"] = $week;
    $data["Installation_Version"] = $version;
    $data["PHP_Version"] = $php;
    $data["Operating_System"] = $os;
    $data["Webserver"] = $webserver;

?>
<html>
      <head>
        <title>Codiad Stats</title>
        <!--Load the AJAX API-->
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
          google.load('visualization', '1.0', {'packages':['corechart']});
          google.load('visualization', '1', {'packages': ['geochart']});
          google.setOnLoadCallback(drawChart);

          function drawChart() {
            <?php
                foreach($data as $title=>$stat) {
                    arsort($stat);
                    $tmp = "var data".$title." = new google.visualization.DataTable();\n";
                    $tmp .= "data".$title.".addColumn('string', 'name');\n";
                    $tmp .= "data".$title.".addColumn('number', 'count');\n";
                    $tmp .= "data".$title.".addRows([";
                    foreach($stat as $key=>$value) {
                        if(trim($key) == '') {
                            $key = "Unknown";
                        }
                        $tmp .= "['".$key."', ".$value."],\n";
                    }
                    $tmp = substr($tmp,0,-1)."]);\n";
                    $tmp .= "var chart = new google.visualization.PieChart(document.getElementById('".$title."'));\n";
                    $tmp .= "chart.draw(data".$title.", {'title':'".str_replace("_"," ",$title)."',pieHole: 0.4,chartArea:{left:10,top:20,width:\"100%\",height:\"100%\"},'width':400, 'height':400});\n";
                                        
                    echo $tmp."\n";
                }
            ?>
            
            var chart = new google.visualization.GeoChart(document.getElementById('worldmap'));
            chart.draw(dataInstallation_Country, {colorAxis:{minValue: 1,  maxValue: 250}});
          }
        </script>
      </head>

      <body>
        <table>
        <th align="left" colspan="<?php echo sizeof($data); ?>"><font style="font-family:Arial;">Codiad IDE - Graphs are rendered based on <?php echo sizeof($ip); ?> unique installations</font></th>
        <tr>
        <?php
            $i = 1;
            foreach($data as $title=>$stat) {
                echo "<td><div id=".$title."></div></td>";
                if($i % 3 == 0) {
                    echo "</tr><tr>";
                }
                $i++;
            }
        ?>
        </tr>
        <td align="center" colspan="<?php echo sizeof($data); ?>"><div id="worldmap" style="width: 95%; height: 95%;"></div></td>
        </table>
      </body>
    </html>
