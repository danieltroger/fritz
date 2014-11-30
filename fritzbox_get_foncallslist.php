<?php
/**
 * Fritz!Box PHP tools CLI script to download the calllist from the Box
 *
 * Must be called via a command line, shows a help message if called without any or an invalid argument
 * v0.3: Changed to download the new csv-file instead of the old xml, which is empty on newer firmwares
 * on older firmwares use fritzbox_get_foncallslist_xml.php instead
 *
 * Check the config file fritzbox.conf.php!
 *
 * @author   Gregor Nathanael Meyer <Gregor [at] der-meyer.de>
 * @license  http://creativecommons.org/licenses/by-sa/3.0/de/ Creative Commons cc-by-sa
 * @version  0.4 2013-01-02
 * @package  Fritz!Box PHP tools
 */


  // load the fritzbox_api class
  require_once('fritzbox_api.class.php');
  $fritz = new fritzbox_api();

  // init the output message
  $message = date('Y-m-d H:i') . ' ';

  // get the phone calls list
  $params = array(
    'getpage'         => '/fon_num/foncalls_list.lua',
    'csv'             => '',
  );
  $output = explode("\n",$fritz->doGetRequest($params));
  $out = Array('total_time' => (int) 0,'total_num' => 0);
  unset($output[0]);
  unset($output[1]);
  unset($output[sizeof($output)-1]);
  foreach($output as $line)
  {
    $csv = str_getcsv ($line, ";");
    if(!empty($csv) && sizeof($csv) >= 6)
    {
      $s = $csv[0];
      $date = $csv[1];
      $length = $csv[6];
      $num = $csv[3];
      $datea = explode(".",$date);
      $month = $datea[1];
      $hm = explode(":",$length);
      $time = ($hm[0]*60)+$hm[1];
      if(!isset($out[$month]))
      {
        $out[$month] = Array('total_time' => (int) 0,'total_num' => (int) 0);
      }
      $out[$month]['total_time'] += $time;
      $out[$month]['total_num']++;
      $out['total_time'] += $time;
      $out['total_num']++;
      if(!isset($out[$month][$num])){$out[$month][$num] = Array('total_time' => (int) 0);}
      $t = &$out[$month][$num];
      $t['total_time'] += $time;
      $t[] = Array('length' => $time,'date'=>$date,'type'=>$s);
    }
  }
  print_r($out);
file_put_contents("calls.json",json_encode($out));

$fritz = null; // destroy the object to log out
?>

