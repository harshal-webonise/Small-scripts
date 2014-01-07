<?php
$basePath = '/home/weboniselab/projects/apps2013/php/Crucible-CRT/app/Plugin';
$crucibleDIRPath='/home/weboniselab/projects/apps2013/php/Crucible/app/Plugin';

$excludeControllers=array('Pages','LogErrors','Settings','Cms');
$plugins = scandir($basePath);
$result='';
foreach ($plugins as $plugin) {
    $exclude = array(
        '.',
        '..',
        'empty',
        '.DS_Store'
    );
    if (in_array($plugin, $exclude)) {
        continue;
    }

    $controllerPath = $basePath . '/' . $plugin . '/Controller';
    //echo "\n".$plugin;

    $isDir = is_dir($controllerPath);
    if (!$isDir) {
        continue;
    }

    $controllers = scandir($controllerPath);


    if (is_array($controllers)) {
        $excludeCont = array(
            '.',
            '..'
        );
        foreach ($controllers as $controller) {
                if (in_array($controller, $excludeCont)) {
                continue;
            }
            $controllerName = $controllerPath . '/' . $controller;
            $fileHandle     = fopen($controllerName, 'r');

            $parts = $plugin;

            while (($buffer = fgets($fileHandle)) !== false) {
                $contClass = preg_replace('/\.php$/', '', $controller);
                if (preg_match('/.*function[\s]+([_a-zA-Z0-9]+)[\s]*\((.*)\)/', $buffer, $matches) && !preg_match('/private/', $buffer, $matches1) && !preg_match('/[\s]*\//', $buffer, $matches1)) {
                    if(($matches[1] != 'beforeFilter') && ($matches[1] != 'beforeRender')){
                    $plugin = uncamelize($parts);
                    $controller = uncamelize(str_replace("Controller", '',$contClass));
                    $action = uncamelize($matches[1]);
                    $result.= $parts.','.$controller .','.$action .  "\n";
                    }

                } elseif (preg_match('/(public)?[\s]+function/', $buffer, $matches) && !preg_match('/private/', $buffer, $matches1) && !preg_match('/[\s]*\/\//', $buffer, $matches1)) {

                    preg_match('/.*function[\s]+([_a-zA-Z0-9]+)[\s]*\((.*)\)/', $buffer, $matches);
                    $contClass = preg_replace('/\.php$/', '', $controller);
                    if (isset($matches[1])&& ($matches[1] != 'beforeFilter') && ($matches[1] != 'beforeRender')) {
                        $plugin = uncamelize($parts);
                        $controller = uncamelize(str_replace("Controller", '',$contClass));
                        $action = uncamelize($matches[1]);


                        $result.= $plugin . ',' . $controller . ',' . $action . "\n";
                    }
                }
            }
            fclose($fileHandle);
        }

    }
}
print_r($result."\n");

$newPath = '/home/weboniselab/data_backup/Crucible_data/ACL/ACl1.4/actions_in_crt_formatted2.csv';
if (($handle1 = fopen($newPath, "w")) !== FALSE) {
    fwrite($handle1, $result, strlen($result));
    fclose($handle1);
}


function uncamelize($camel, $splitter = "_") {
    $camel = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter . '$0', $camel));
    return trim(strtolower($camel));
}
