#!/usr/bin/env php
<?php
// worst unreadable shit but works
// dial #96*7* on your fritz box connected phone to get this working.
$h = fsockopen("fritz.box",23);
sleep(1);
fputs($h,"leinad" . PHP_EOL);
echo "Connected." . PHP_EOL;
stream_set_blocking($h,0);
$cmd = "echo \$((\$(ctlmgr_ctl r nqos settings/stat/ds_current_bps|sed 's/,.*//')*8 ))" . PHP_EOL;
$s = 0;
while(1)
{
  $d = fgets($h);
  if($d != false)
  {
    if($s == 2)
    {
      echo formatsize(str_replace("\n","",str_replace("\r","",$d))) . "/s\r";
      $s = 0;
    }
    if($s)
    {
      $s = 2;
    }
    if($d == "# ")
    {
      fputs($h,$cmd);
      $s = 1;
    }
  }
  usleep(5000);
}
fclose($h);
function formatsize($size)
{
  if($size < 1) return false;
  if($size > 1024 * 1024 * 1024)
  {
    return round($size / 1024 / 1024 / 1024,2) . " GiB";
  }
  elseif($size > 1024 * 1024)
  {
    return round($size / 1024 / 1024,2) . " MiB";
  }
  elseif($size > 1024)
  {
    return round($size / 1024,2) . " KiB";
  }
  elseif($size < 1024)
  {
    return "{$size} Bytes";
  }
}
