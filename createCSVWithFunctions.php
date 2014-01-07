<?php
/**
 * Created by Webonise Lab.
 * User: Harshal <harshal@weboniselab.com>
 * Date: 6/1/14 6:29 PM
 */

$basePath = '/home/weboniselab/data_backup/Crucible_data/ACL/ACl1.4/actions_in_crt.csv';
$newPath = '/home/weboniselab/data_backup/Crucible_data/ACL/ACl1.4/formatted_actions_crt.csv';
if (($handle = fopen($basePath, "r")) !== FALSE) {
    $newString = '';
    //$newString .= 'product,module,submodule,plugin,controller,action' ."\n";
    while (($data = fgetcsv($handle, 1000, ",", '"')) !== FALSE) {
        $plugin = trim($data[0]);
        $controller = trim($data[1]);
        $action = trim($data[2]);
        $controller = uncamelize(str_replace("Controller", '', $controller));

        // echo $controller . "\n";
        $newString .=  $plugin . ',' . $controller . ',' . $action . "\n";

    }
}
echo $newString;
if (($handle1 = fopen($newPath, "w")) !== FALSE) {
    fwrite($handle1, $newString, strlen($newString));
}
fclose($handle1);


function uncamelize($camel, $splitter = "_") {
    $camel = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter . '$0', $camel));
    return strtolower($camel);
}

?>
