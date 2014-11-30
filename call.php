#!/usr/local/php5/bin/php
<?php
$out = Array();
$h = fopen("http://fritz.box/fon_num/foncalls_list.lua?sid=2046413e42eb4211&csv=","r");
fseek ($h,66); // seek to the begin of the list
while(!feof($h))
{
  $d = substr(fgets($h),0,-1);
  $csv = str_getcsv ($d, ";");
  $date = @$csv[1];
  $length = @$csv[6];
  if(!empty($date) && !empty($length))
  {
    $date = explode(".",$date);
    $month = $date[1];
    $hm = explode(":",$length);
    $time = ($hm[0]*60)+$hm[1];
    if(!isset($out[$month]))
    {
      $out[$month] = 0;
    }
    $out[$month] += $time;
  }
}
fclose($h);
print_r($out);
