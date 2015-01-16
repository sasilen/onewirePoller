#!/usr/bin/php -q
<?php
set_error_handler('handleError');
openlog('oneWire',LOG_NDELAY, LOG_USER);

date_default_timezone_set('Europe/Helsinki');
$options = getopt("t:d");

DEFINE ("LOG2FILE",1);
DEFINE ("LOG2JSON",1);
DEFINE ("UPDATE",1);
DEFINE ("LOGDIR","/var/www/logger/");
DEFINE ("LOGURL","<json url>");
DEFINE ("OWDIR","/media/1-wire/");
DEFINE ("DEBUG",0);

$devices['28.FFEC65760400'] = array('sensor'=>array('temperature'),'description'=>'livingroom temperature','tags'=>array('livingroom','temperature'));
$devices['12.2E30B7000000'] = array('sensor'=>array('latch.B'),'description'=>'livingroom','tags'=>array('livingroom','door'));

$files = scandir(OWDIR,1);
foreach($files as $sensor) :
  if(preg_match('/[0-9][0-9]\./', $sensor))
    if (!isset($devices[$sensor]))
      dbug("Not known : ".$sensor." *",4);
    $sensors[] = $sensor;
endforeach;
unset ($files);

sort($sensors);
foreach ($sensors as $sensor) :
  if (isset($devices[$sensor])) {
    if (UPDATE && LOG2JSON) {
       $jsondata = array('Sensor'=>array('datetime'=>date('Y-m-d H:i'),
                     'type'=>$devices[$sensor]['sensor'][0],
                     'description'=>$devices[$sensor]['description'],
                     'id'=>$sensor,
                    ));
       log2json($jsondata,LOGURL.'/sensors/add.json');
    }
    readsensor($sensor);
  }
endforeach;

function readsensor($sensor) {
  $update=true;
  global $devices;
  foreach ($devices[$sensor]['sensor'] as $ant) :
    $actions = null;
    $data = round(trim(file_get_contents(OWDIR.'/'.$sensor.'/'.$ant,true)),2);
    if (substr($ant,0,5)=='latch' && $data==1) {
      file_put_contents(OWDIR."/".$sensor.'/'.$ant,0);
    } elseif (substr($ant,0,5)=='latch' && $data==0) {
      $update = false; 
      $actions .= "nochange";
    }
    if (UPDATE && $update) {
      if (LOG2FILE) {
        log2file($sensor,$ant,$data);
        $actions .= "log2file ";
      }
      if (LOG2JSON) {
        $jsondata = array('SensorValue'=>array('datetime'=>date('Y-m-d H:i'),
                                            'sensor'=>$ant,
                                            'value'=>$data,
                                            'device_id'=>$sensor,
                                           ));
        log2json($jsondata,LOGURL.'/sensor_values/add.json');
        $actions .= "log2json";
      }
    }
    dbug($sensor.' : '.str_pad($ant,11).' = '.str_pad($data,6). "(".$devices[$sensor]['description'].") : ".$actions);
  endforeach;
}

function log2file($sensor,$ant,$data) {
  file_put_contents(LOGDIR.strtolower($sensor).".".$ant.".log", date('Y-m-d H:i').",".trim(round($data,1))."\n", FILE_APPEND | LOCK_EX);
}

function log2json($data,$url) {
  $data_string = json_encode($data);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
  );
  $result = curl_exec($ch);
}

function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
  // error was suppressed with the @-operator
  if (0 === error_reporting()) {
    return false;
  }
  throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

function dbug($message,$level=7) {
  /*** LEVELS
  Emergency     (level 0)
  Alert         (level 1)
  Critical      (level 2)
  Error         (level 3)
  Warning       (level 4) 
  Notice        (level 5) What we did (actions)
  Info          (level 6) What actions was performed
  Debug         (level 7) Detailed
  **/
  syslog($level, $message);
  if ($level<3) die('Level Critical -> die');
  
  if (DEBUG) {
    if ($level==4) echo "\033[34m * [warn] \033[0m". $message. "\n";
    elseif ($level<4) echo "\033[31m * [err ] \033[0m" . $message ."\n";
    else echo "\033[32m * [ ok ] \033[0m" .$message . "\n";
  }
  return true;
}
