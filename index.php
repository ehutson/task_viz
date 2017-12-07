<html>
<head>
<style>
pre {
  font-family: "courier new", courier, monospace;
  font-size: 9px;
}
</style>
</head>
<body>

<form method="POST">
Mailing Id:
&nbsp;&nbsp;
<input type="text" name="mailingid"  value="<?php echo $_POST['mailingid'];?>"/>
&nbsp;&nbsp;
<input type="submit" value="Submit" />
</form>


<?php

$username = '[MY_ORACLE_USER]';
$password = '[MY_ORACLE_PASSWORD]';
$connectionString = '[ORACLE_CONNECTION_STRING]';  // For example:  Pod 5 is dbsd5i.atlis1/sd5i


$mailingid = $_POST['mailingid'];
if (!$mailingid) die();


$conn = oci_connect($username, $password, $connectionString);
if (!$conn) {
  $e = oci_error();
  trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

$metaQuery = 'select min(start_task_ts) as mints, max(start_remove_ts) as maxts, count(*) as count from ss_task_history where mailing_id = ' . $mailingid;
$dataQuery = 'select task_id, start_task_ts as start_ts, start_remove_ts as end_ts from sdm.ss_task_history where mailing_id = ' . $mailingid . ' order by task_id asc';
graphIt($conn, $metaQuery, $dataQuery);

function graphIt($conn, $metaQuery, $dataQuery) {

  $st = oci_parse($conn, $metaQuery);
  oci_execute($st);
  $r = oci_fetch_object($st);
  
  $mints = getTimestamp($r->MINTS);
  $maxts = getTimestamp($r->MAXTS);
  $slots = ceil($maxts - $mints);
  $rows = getDigitCount($r->COUNT);

  echo "<div><strong>Started:</strong>  " . date('m/d/Y H:i:s', $mints) . "</div>";
  echo "<div><strong>Finished:</strong>  " . date('m/d/Y H:i:s', $maxts) . "</div>";

  $st = oci_parse($conn, $dataQuery);
  oci_execute($st);
  
  echo "<pre>";

  while (($row = oci_fetch_object($st)) != false) {
    $task_id = $row->TASK_ID;
    $start = getTimestamp($row->START_TS);
    $end = getTimestamp($row->END_TS);
  
    $ss = ceil($start - $mints);
    $se = ceil($end - $mints);
  
    echo sprintf('%0'.$rows.'d ', $task_id);
    for ($i = 1; $i <= $slots; $i++) {
      if ($i >= $ss && $i <= $se) {
        echo "X";
      } else {
        echo "_";
      }
    }
    echo "<BR>";
  }

  echo "</pre>";
}

function getTimestamp($timeString) {
  $t = DateTime::createFromFormat('d-M-y h.i.s.u A', $timeString);
  return $t->getTimestamp() + $t->format('u')/1000000;;
}

function getDigitCount($num) {
  $d = 0;
  if ($num < 1) $d = 1;
  while($num) {
    $num  = floor($num / 10);
    $d++;
  }
  return $d;
}

?>
</body></html>
