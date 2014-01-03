<?php
$basePath = '/home/weboniselab/projects/apps2013/php/Crucible-CRT/app/';

$appLevelExcludeArray = array(
    'Config', 'Console', 'Lib', 'Locale', 'Model', 'Test', 'tmp',
    'Vendor', 'webroot', 'CHAHNGELOG.md', 'dummy.ctp', 'index.ctp', 'migration.sh'
);

$pluginLevelExcludeArray = array(
    '.', '..',
    'Acl', 'CakeResque', 'Cms', 'EmailTemplates',
    'LogErrors', 'MailQueues', 'Migrations', 'Sluggable', 'Trackings', 'CHANGELOG.md', 'empty'
);
$exclude                 = array('.', '..', 'Component');

$appLevelFolders     = scandir($basePath);
$appLevelControllers = array();

$completeControllerData = array();
$dataAppLevel           = array();

$myhtml = file_get_contents('/home/weboniselab/projects/apps2013/php/Crucible-CRT/app/Plugin/Crpts/View/Cfas/add.ctp');

$doc = new DOMDocument();
$doc->loadHTML($myhtml);

$tags = $doc->getElementsByTagName('label');

$tagArray = array();
foreach ($tags as $tag) {
    $tagArray[] = $tag->nodeValue;
}
print_r($tagArray);



foreach ($appLevelFolders as $appLevelFolder) {

    if (in_array($appLevelFolder, $appLevelExcludeArray)) {
        continue;
    }
    $appLevelControllersBasePtah = $basePath . 'Controller';
    if (!is_dir($appLevelControllersBasePtah)) {
        continue;
    }
    $appLevelControllers = scandir($appLevelControllersBasePtah);

    foreach ($appLevelControllers as $appLevelController) {

        if (in_array($appLevelController, $exclude)) {
            continue;
        }
        $dataAppLevel[$appLevelController] = getControllerMessages($appLevelControllersBasePtah, $appLevelController);

    }


    /* $appLevelViewsPath = $basePath . 'View';

     if (!is_dir($appLevelViewsPath)) {
         continue;
     }
     $appLevelViews = scandir($appLevelViewsPath);*/

    //app level
    /* foreach ($appLevelViews as $appLevelView) {

     }*/
}
$completeControllerData['AppLevelControllers'] = $dataAppLevel;

$appLevelPluginsPath = $basePath . 'Plugin';
$appLevelPlugins     = scandir($appLevelPluginsPath);

$pluginLevelControllerData = array();
foreach ($appLevelPlugins as $pluginId => $pluginName) {

    if (in_array($pluginName, $pluginLevelExcludeArray)) {
        continue;
    }
    //print_r("**********'$pluginName'************\n");
    $pluginBasePath = $appLevelPluginsPath . '/' . $pluginName;

    $pluginLevelControllersBasePath = $pluginBasePath . '/Controller/';
    $pluginLevelControllers         = scandir($pluginLevelControllersBasePath);


    $pluginLevelViewsBasePath = $pluginBasePath . '/View/';
    $pluginLevelViews         = scandir($pluginLevelViewsBasePath);

    // for each controller at app level

    foreach ($pluginLevelControllers as $key => $pluginLevelController) {

        if (in_array($pluginLevelController, $exclude)) {
            continue;
        }
        $dataPluginLevel                                                =
            getControllerMessages($pluginLevelControllersBasePath, $pluginLevelController);
        $pluginLevelControllerData[$pluginName][$pluginLevelController] = $dataPluginLevel;

    }

    foreach ($pluginLevelViews as $pluginLevelView) {
        if (in_array($pluginLevelView, $exclude)) {
            continue;
        }
        $viewFolderPath = $pluginLevelViewsBasePath . '/' . $pluginLevelView;
        $views          = scandir($viewFolderPath);
        foreach ($views as $view) {
            if (in_array($view, $exclude)) {
                continue;
            }
            getViewsStaticData($viewFolderPath, $view);

        }
    }
}
$completeControllerData['PluginLevelControllers'] = $pluginLevelControllerData;

//echo json_encode($completeControllerData);
//print_r($completeControllerData);
//print_r('-----------------------end---------------------');


function isDIR() {

}

function getControllerMessages($path, $controllerName) {
    $fileHandle         = fopen($path . '/' . $controllerName, 'r');
    $controllerMessages = array();
    $functionName       = '';
    while (($buffer = fgets($fileHandle)) !== false) {
        if (preg_match('/.*function[\s]+([_a-zA-Z0-9]+)[\s]*\((.*)\)/', $buffer,
            $matches) && !preg_match('/private/', $buffer, $matches1) && !preg_match('/[\s]*\//', $buffer,
            $matches1)
        ) {
            $functionName = $matches[1];
        }
        if (preg_match('/this->Session->setFlash+[a-z(__]*\'([a-zA-Z\s.,]*)\'/', $buffer,
            $matches2)
        ) {
            $controllerMessages[][$functionName] = $matches2[1];
        }
    }

    return $controllerMessages;
}

function getViewsStaticData($path, $viewName) {

    $fileHandle = fopen($path . '/' . $viewName, 'r');
    $viewPlaceHolders = array();
    while (($buffer = fgets($fileHandle)) !== false) {
        if (preg_match('/[\'|"]placeholder[\'|"]+[\s=>\']*([a-zA-Z\s-]*)[\'|"]/', $buffer,
            $placeHolderMatches)
        ) {
            if (!empty($placeHolderMatches[1]) && isset($placeHolderMatches[1])) {
                $viewPlaceHolders[$viewName][] = $placeHolderMatches[1];
            }

        }
        //      if(preg_match('/this->Html->link[(_\']*([a-z-A-Z\s]*)[\')]*/',$buffer,$matches)){
        //           print_r($matches);
        //        }

        if ((preg_match('/this->Html->link[(].*/', $buffer,
            $linkMatches))
        ) {
            $data[$path][$viewName][] = ($linkMatches[0]);

        }

        if (preg_match('/[\'|"]title[\'|"]+[\s=>\']*([a-zA-Z\s-]*)[\'|"]/', $buffer,
            $titleMatches)
        ) {
            if (!empty($titleMatches[1]) && isset($titleMatches[1])) {
                $viewTitles[$viewName][] = $titleMatches[1];
            }

        }
        if (preg_match('/this->Form->submit[(\']*([a-zA-Z0-9\s]*)[\']*[,\sa-zA-Z0-9)(\'=>-]*/', $buffer,
            $submitButtonMatches)
        ) {
            if (!empty($submitButtonMatches[1]) && isset($submitButtonMatches[1])) {
                $viewSubmitButtonMatches[$viewName][] = $submitButtonMatches[1];
            }

        }
    }
    $myhtml = file_get_contents($path . '/' . $viewName);
    $doc = new DOMDocument();
    if(!empty($myhtml)){
    libxml_use_internal_errors(true);
    $doc->loadHTML($myhtml);
    $tags = $doc->getElementsByTagName('label');

    $tagArray = array();
    foreach ($tags as $tag) {
        $tagArray[$viewName][] = $tag->nodeValue;
    }

    if (isset($tagArray) && !empty($tagArray)) {
        print_r($tagArray);
    }
    }
}
