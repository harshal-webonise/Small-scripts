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
        getControllerMessages($appLevelControllersBasePtah, $appLevelController);

    }
    $appLevelPluginsPath = $basePath . 'Plugin';

    if (!is_dir($appLevelPluginsPath)) {
        continue;
    }
    $appLevelPlugins = scandir($appLevelPluginsPath);

    $appLevelViewsPath = $basePath . 'View';

    if (!is_dir($appLevelViewsPath)) {
        continue;
    }
    $appLevelViews = scandir($appLevelViewsPath);

    //app level
    /* foreach ($appLevelViews as $appLevelView) {

     }*/
}
//foreach plugin
$data = array();
foreach ($appLevelPlugins as $pluginId => $pluginName) {

    if (in_array($pluginName, $pluginLevelExcludeArray)) {
        continue;
    }
    print_r("**********'$pluginName'************\n");
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
        getControllerMessages($pluginLevelControllersBasePath, $pluginLevelController);

    }
    foreach ($pluginLevelViews as $pluginLevelView) {
        if (in_array($pluginLevelView, $exclude)) {
            continue;
        }
        $viewFolderPath=$pluginLevelViewsBasePath.'/'.$pluginLevelView;
        $views=scandir($viewFolderPath);
        foreach ($views as $view){
            if (in_array($view, $exclude)) {
                continue;
            }
            getViewsStaticData($viewFolderPath, $view);
        }
    }


}
//print_r($data);
print_r('-----------------------end---------------------');


function isDIR() {

}

function getControllerMessages($path, $controllerName) {
    //    print_r("Controller = ");
    //    print_r($controllerName . "\n");
    //        $data[$pluginId][$key]['controller']=$pluginLevelController;
    $fileHandle = fopen($path . '/' . $controllerName, 'r');

    while (($buffer = fgets($fileHandle)) !== false) {
        if (preg_match('/.*function[\s]+([_a-zA-Z0-9]+)[\s]*\((.*)\)/', $buffer,
            $matches) && !preg_match('/private/', $buffer, $matches1) && !preg_match('/[\s]*\//', $buffer,
            $matches1)
        ) {
            //            print_r("\n" . $matches[1] . "\n");
            //                $data[$pluginId][$key]['action_name']=$matches[1];
        }
        if (preg_match('/this->Session->setFlash+[a-z(__]*\'([a-zA-Z\s.,]*)\'/', $buffer,
            $matches2)
        ) {
            //            print_r($matches2[1] . "\n");
            //                $data[$pluginId][$key]['messages'][]=$matches2[1];

        }
    }
}

function getViewsStaticData($path, $viewName) {
    print_r($viewName."\n");
    $fileHandle = fopen($path . '/' . $viewName, 'r');

    while (($buffer = fgets($fileHandle)) !== false) {
        if (preg_match('/[\'|"]placeholder[\'|"]+[\s=>\']*([a-zA-Z\s-]*)[\'|"]/', $buffer,
            $matches)
        ) {
                        print_r($matches);

        }
    }
}