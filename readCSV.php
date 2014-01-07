<?php
/**
 * Created by Webonise Lab.
 * User: Harshal <harshal@weboniselab.com>
 * Date: 6/1/14 6:21 PM
 */
$basePath='/home/weboniselab/data_backup/Crucible_data/ACL/ACL1.3/action_in_crt_1.3.csv';
$newActionsPath='/home/weboniselab/data_backup/Crucible_data/ACL/ACl1.4/formatted_actions_crt.csv';
$diffActionsPath='/home/weboniselab/data_backup/Crucible_data/ACL/ACl1.4/diff_actions_crt_1.4.csv';
$file = fopen($basePath, 'r');
$dataArray=array();
$oldActions = array();
while (($line = fgetcsv($file, ",", "\t")) !== false) {
    //$line is an array of the csv elements
    $dataArray[]= array('plugin'=>uncamelize($line[0]), 'controller'=>uncamelize($line[1]),'action'=>uncamelize($line[2]));
  //  $oldActions[uncamelize($line[0])][uncamelize($line[1])][uncamelize($line[2])] = true;

}

$dataArray = arrayUnique($dataArray);
echo "\n===========> Old Actions: " . count($dataArray) . " <========================";
//print_r($dataArray);
fclose($file);
$fp=fopen($newActionsPath,'r');
$newActionsArray=array();
$newActions = array();
while (($line1 = fgetcsv($fp, ",")) !== false) {
    //$line is an array of the csv elements
    if(($line1[2] != 'beforeFilter') && ($line1[2] != 'beforeRender')) {
        $newActionsArray[]= array('plugin'=>uncamelize($line1[0]), 'controller'=>uncamelize($line1[1]),'action'=>uncamelize($line1[2]));
    }
}
$newActionsArray = arrayUnique($newActionsArray);
echo "\n\n===========> New Actions: " . count($newActionsArray) . " <========================";
//print_r($newActionsArray);
//$diff=array_diff_assoc($dataArray,$newActionsArray);
$count = 0;
$resultString='';
foreach ($newActionsArray as $action) {
    //print_r($action);
    //echo "\n=====>".$count++;
  /*  if(!isset($oldActions[$action['plugin']][$action['controller']][$action['action']])) {
        $diff[] = $action;
    }*/
    if (!in_array($action, $dataArray)) {
        $diff[] = $action;
        $resultString.=$action['plugin'].','.$action['controller'].','.$action['action']."\n";
    }
}

if (($handle1 = fopen($diffActionsPath, "w")) !== FALSE) {
    fwrite($handle1, $resultString, strlen($resultString));
fclose($handle1);
}


echo "\n\n===========> Diff Actions: " . count($diff) . " <========================";
print_r($diff);


function uncamelize($camel, $splitter = "_") {
    $camel = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter . '$0', $camel));
    return trim(strtolower($camel));
}


/**
 * Create Unique Arrays using an md5 hash
 *
 * @param array $array
 * @return array
 */
function arrayUnique($array, $preserveKeys = false)
{
    // Unique Array for return
    $arrayRewrite = array();
    // Array with the md5 hashes
    $arrayHashes = array();
    foreach($array as $key => $item) {
        // Serialize the current element and create a md5 hash
        $hash = md5(serialize($item));
        // If the md5 didn't come up yet, add the element to
        // to arrayRewrite, otherwise drop it
        if (!isset($arrayHashes[$hash])) {
            // Save the current element hash
            $arrayHashes[$hash] = $hash;
            // Add element to the unique Array
            if ($preserveKeys) {
                $arrayRewrite[$key] = $item;
            } else {
                $arrayRewrite[] = $item;
            }
        }
    }
    return $arrayRewrite;
}


/*function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}*/

