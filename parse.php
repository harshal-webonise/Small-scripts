<?php
$basePath = '/home/webonise/projects/apps2013/php/Crucible-CRT/app/';

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

$viewResultString='';
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


     $appLevelViewsPath = $basePath . 'View';

     if (!is_dir($appLevelViewsPath)) {
         continue;
     }
     $appLevelViews = scandir($appLevelViewsPath);

    //app level
     foreach ($appLevelViews as $appLevelView) {
         if (in_array($appLevelView, $exclude)) {
             continue;
         }
         $viewFolderPath = $appLevelViewsPath . '/'.$appLevelView;
         $views          = scandir($viewFolderPath);
         foreach ($views as $view) {
             if (in_array($view, $exclude)) {
                 continue;
             }
             $viewContent=getViewsStaticData($viewFolderPath, $view);

             $viewsContents['app'][$appLevelView][]=$viewContent;

             if(!empty($viewContent)){
                 $viewResultString.='app'.'$$$'.$appLevelView.'$$$'.$viewContent."\n";
             }
         }
     }
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
        $viewFolderPath = $pluginLevelViewsBasePath . $pluginLevelView;
        $views          = scandir($viewFolderPath);
        foreach ($views as $view) {
            if (in_array($view, $exclude)) {
                continue;
            }
            $viewContent=getViewsStaticData($viewFolderPath, $view);

            $viewsContents[$pluginName][$pluginLevelView][]=$viewContent;

            if(!empty($viewContent)){
            $viewResultString.=$pluginName.','.$pluginLevelView.','.$viewContent."\n";
            }
        }
    }
}
die;
$completeControllerData['PluginLevelControllers'] = $pluginLevelControllerData;
//print_r(json_encode($viewsContents));
//file_put_contents("/home/weboniselab/projects/apps2013/php/testApp/test.smallapp.com/view_data.json",json_encode($viewsContents));
//file_put_contents("/home/weboniselab/projects/apps2013/php/testApp/test.smallapp.com/controller_data.json",json_encode($completeControllerData));

print_r('-----------------------end---------------------');


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
    $alertMessagesArray=array();
    $parseData=array();
    $resultString='';
    while (($buffer = fgets($fileHandle)) !== false) {
        if (preg_match('/\'placeholder\'[^\']+\'([^\'"]+)\'/', $buffer,
            $placeHolderMatches)
        ) {
            if (!empty($placeHolderMatches[1]) && isset($placeHolderMatches[1])) {
                $viewPlaceHolders[$viewName]['place_holders'][] = $placeHolderMatches[1];
                $parseData[$path][$viewName]['place_holders'][] = $placeHolderMatches[1];
                $resultString.=$path.'|$$$|'.$viewName.'|$$$|'.'place_holder'.'|$$$|'.$placeHolderMatches[1]."\n";
            }

        }

        if ((preg_match('/this->Html->link[(].*/', $buffer,
            $linkMatches))
        ) {
            $data[$path][$viewName]['link'][] = ($linkMatches[0]);
            $parseData[$path][$viewName]['link'][] = ($linkMatches[0]);
            $resultString.=$path.'|$$$|'.$viewName.'|$$$|'.'link'.'|$$$|'.$linkMatches[0]."\n";

        }

        if (preg_match('/\'title\'[^\']+\'([^\'"]+)\'/', $buffer,
            $titleMatches)
        ) {
            if (!empty($titleMatches[1]) && isset($titleMatches[1])) {
                $viewTitles[$viewName]['title'][] = $titleMatches[1];
                $parseData[$path][$viewName]['title'][] = $titleMatches[1];
                $resultString.=$path.'|$$$|'.$viewName.'|$$$|'.'title'.'|$$$|'.$titleMatches[1]."\n";
            }

        }
        if (preg_match('/this->Form->submit[(\']*([a-zA-Z0-9\s]*)[\']*[,\sa-zA-Z0-9)(\'=>-]*/', $buffer,
            $submitButtonMatches)
        ) {
            if (!empty($submitButtonMatches[1]) && isset($submitButtonMatches[1])) {
                $viewSubmitButtonMatches[$viewName]['submit'][] = $submitButtonMatches[1];
                $parseData[$path][$viewName]['submit'][] = $submitButtonMatches[1];
                $resultString.=$path.'|$$$|'.$viewName.'|$$$|'.'Button'.'|$$$|'.$submitButtonMatches[1]."\n";
            }

        }

//        message:"([^"]+)"
        if (preg_match('/message:"([^"]+)"/', $buffer,
            $validationMessages)
        ) {
            if (!empty($validationMessages[1]) && isset($validationMessages[1])) {
                $viewValidationMessages[$viewName]['validation_messages'][] = $validationMessages[1];
                $parseData[$path][$viewName]['validation_messages'][] = $validationMessages[1];
                $resultString.=$path.'|$$$|'.$viewName.'|$$$|'.'validation_messages'.'|$$$|'.$validationMessages[1]."\n";
            }

        }


        if (preg_match('/alert[^)]\'([^\']+)\'/', $buffer,
            $alertMessagesMatches)
        ) {
            if (!empty($alertMessagesMatches[1]) && isset($alertMessagesMatches[1])) {
                $alertMessagesArray[$viewName]['alert_messages'][] = $alertMessagesMatches[1];
                $parseData[$path][$viewName]['alert_messages'][] = $alertMessagesMatches[1];
                $resultString.=$path.'|$$$|'.$viewName.'|$$$|'.'alert_message'.'|$$$|'.$alertMessagesMatches[1]."\n";
            }
        }

        if (preg_match('/<div[^=]+="toolTipInner">([^<])+$/', $buffer,
            $toolTipMsgMatches)
        ) {
                if (!empty($toolTipMsgMatches[0]) && isset($toolTipMsgMatches[0])) {
                $toolMessagesArray[$viewName]['tool_tip'][] = $toolTipMsgMatches[0];
                    $parseData[$path][$viewName]['tool_tip'][] = $toolTipMsgMatches[0];
                $resultString.=$path.'|$$$|'.$viewName.'|$$$|'.'tool_tip'.'|$$$|'.$toolTipMsgMatches[0]."\n";
            }

        }

        if (preg_match('/<p[^>]+>([^<]+)<\/p>/', $buffer,
            $paraMsgMatches)
        ) {
            if (!empty($paraMsgMatches[0]) && isset($paraMsgMatches[0])) {
                $toolMessagesArray[$viewName]['para_message'][] = $paraMsgMatches[0];
                $parseData[$path][$viewName]['para_message'][] = $paraMsgMatches[0];
                $resultString.=$path.'|$$$|'.$viewName.'|$$$|'.'para_message'.'|$$$|'.$paraMsgMatches[0]."\n";
            }
        }
    }

    $myhtml = file_get_contents($path . '/' . $viewName);
    $doc = new DOMDocument();
    if(!empty($myhtml)){
    libxml_use_internal_errors(true);
    $doc->loadHTML($myhtml);
    $tags = $doc->getElementsByTagName('label');
    $options = $doc->getElementsByTagName('select');
    $h1Messages=$doc->getElementsByTagName('h1');
    $divMessages=$doc->getElementsByTagName('div');
    $h2Messages=$doc->getElementsByTagName('h2');
    $h3Messages=$doc->getElementsByTagName('h3');
    $h4Messages=$doc->getElementsByTagName('h4');
    $h5Messages=$doc->getElementsByTagName('h5');
    $h6Messages=$doc->getElementsByTagName('h6');
    $spanMessages=$doc->getElementsByTagName('span');

        $fullViewPath=$path.'|$$$|'.$viewName.'|$$$|';

        $dataArray= formattedData($divMessages,'div');
        if(!empty($dataArray) && isset($dataArray)){
            foreach($dataArray as $actualData){
                $resultString.=$fullViewPath.'|$$$|'.'div'.$actualData."\n";
            }
            $parseData[$path][$viewName]['div']=$dataArray;
        }
        $dataArray= formattedData($tags,'label');
        if(!empty($dataArray)){
            $parseData[$path][$viewName]['label']=$dataArray;
        }
        $dataArray= formattedData($options,'select');
        if(!empty($dataArray)){
            $parseData[$path][$viewName]['select']=$dataArray;
        }
        $dataArray= formattedData($h1Messages,'h1');
        if(!empty($dataArray)){
            $parseData[$path][$viewName]['h1']=$dataArray;
        }
        $dataArray= formattedData($h2Messages,'h2');
        if(!empty($dataArray)){
            $parseData[$path][$viewName]['h2']=$dataArray;
        }
        $dataArray= formattedData($h3Messages,'h3');
        if(!empty($dataArray)){
            $parseData[$path][$viewName]['h3']=$dataArray;
        }
        $dataArray= formattedData($h4Messages,'h4');
        if(!empty($dataArray)){
            $parseData[$path][$viewName]['h4']=$dataArray;
        }

        $dataArray= formattedData($h5Messages,'h5');
        if(!empty($dataArray)){
            $parseData[$path][$viewName]['h5']=$dataArray;
        }
        $dataArray= formattedData($h6Messages,'h6');
        if(!empty($dataArray)){
            $parseData[$path][$viewName]['h6']=$dataArray;
        }
        $dataArray= formattedData($spanMessages,'span');
        if(!empty($dataArray)){
            $parseData[$path][$viewName]['span']=$dataArray;
        }
    }
    if(!empty($parseData)){
        print_r($parseData);
    }

    return $resultString;
}

function formattedData($object,$attribute){
    $resultString='';
    $result=array();
    foreach($object as $data){
        $divArray[$attribute][] = $data->nodeValue;
        $dataArray = getCompleteArray(strip_unnecessary_content($data->nodeValue));
        if(!empty($dataArray)) {
            $result[] = $dataArray;
        }
//        $resultString.=$fileName.','.$attribute.','.strip_tags($data->nodeValue)."\n";
    }
    return reset($result);
}


function getCompleteArray($data) {
    $newData = null;
    foreach($data as $value) {
        $value = trim($value);
        if(!empty($value)) {
            $newData[] = $value;
        }
    }
    return $newData;
}




function strip_unnecessary_content($data){
    //$data = htmlentities($data, null, 'utf-8');
    $data = str_replace("&nbsp;", "", $data);
    $data = trim(strip_tags($data));
    return explode('#####', trim(preg_replace("/\r\n|\r|\n/", '#####', $data)));
}