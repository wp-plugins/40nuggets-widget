<?php

if (!isset($_GET["type"])) return;

$type = "audience";

require_once(__FILE__ . '../Fortynuggets_Plugin.php');

$plugin = new Fortynuggets_Plugin ();	
$response = $plugin->apiCall("clients/me/users?fields=id^email^name^stats&count=100000000");
$users = $response->users;

download_send_headers($type . "_export_" . date("Y-m-d") . ".csv");
echo array2csv($users);





function download_send_headers($filename) {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

 ?>