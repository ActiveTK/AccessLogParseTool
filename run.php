<?php

  $tempfile = $_FILES['logfile']['tmp_name'];

  if (isset($_FILES['logfile']['error']) && is_int($_FILES['logfile']['error']) && is_uploaded_file($_FILES['logfile']['tmp_name']))
  {
    define('filename', '/home/activetk/activetk.cf/uploads/upload_' . md5_file($_FILES['logfile']['tmp_name']) . ".log");
    if (!move_uploaded_file($_FILES['logfile']['tmp_name'], filename))
    {
      die("ファイルをアップロードできませんでした");
    }
  }
  else
  {
    die("ファイルが選択されていません");
  }

  $starttime = microtime(true);
  $kai = 0;
  $log = array();
  $allsize = 0;

  $input_fh = fopen(filename, "r");
  if ($input_fh) {
    while(($line = fgets($input_fh)) !== false) {

      $kai++;

      $logs = explode(" ", $line);
      $ip = $logs[1];
      $time = $logs[4] . "]";
      $prof = $logs[6];

      if (!isset($log[$ip]))
      {
        $log[$ip] = array();
        $log[$ip]["times"] = 1;
        $log[$ip]["access-first"] = $time;
        $log[$ip]["access-last"] = $time;
        $log[$ip]["protocol"] = $logs[8];
        $log[$ip]["path"] = $logs[7];
        $log[$ip]["size"] = $logs[10];
        $log[$ip]["from"] = $logs[11];
        $log[$ip]["useragent"] = $logs[12];
      }
      else
      {
        $log[$ip]["times"] += 1;
        $log[$ip]["access-last"] = $time;
        $log[$ip]["size"] += $logs[10];
      }
      $allsize += $logs[10];

    }
    fclose($input_fh);
    echo "<h1>解析が完了しました！</h1>\n";
    echo "<p>ログファイルの行数は <b>" . $kai . "行</b> 、かかった時間は <b>" . (microtime(true) - $starttime) . "秒</b> です。</p>\n";
    echo "<p>解析速度は <b>" . ($kai / (microtime(true) - $starttime)) . "/s</b> です。</p>\n";
    echo "<p>総転送量は <b>{$allsize}バイト</b> です。</p>\n";
    echo "<br>";
    echo "<pre>";
    var_dump($log);
    echo "</pre>";
    exit();
  }
  else
  {
    die("ファイルが選択されていません");
  }
