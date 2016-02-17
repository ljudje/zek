<?php

//  define('SHOW_VARIABLES', 1);
//  define('DEBUG_LEVEL', 1);

 error_reporting(E_ALL ^ E_NOTICE);
 ini_set('display_errors', 'On');

set_include_path('.' . PATH_SEPARATOR . get_include_path());


include_once dirname(__FILE__) . '/' . 'components/utils/system_utils.php';

//  SystemUtils::DisableMagicQuotesRuntime();

SystemUtils::SetTimeZoneIfNeed('Europe/Belgrade');

function GetGlobalConnectionOptions()
{
    return array(
  'server' => 'HOST',
  'port' => '3306',
  'username' => 'USER',
  'password' => 'PASS',
  'database' => 'NAME'
);
}

function HasAdminPage()
{
    return true;
}

function GetPageGroups()
{
    $result = array('<i class="kre3m kre3m-pages"></i>Vsebina', '<i class="kre3m kre3m-settings"></i>Nastavitve');
    return $result;
}

function GetPageInfos()
{
    $result = array();
    $result[] = array('caption' => 'Strani', 'short_caption' => 'Strani', 'filename' => '_page.php', 'name' => '_page', 'group_name' => '<i class="kre3m kre3m-pages"></i>Vsebina', 'add_separator' => false);
    $result[] = array('caption' => 'Vsebina', 'short_caption' => 'Vsebina', 'filename' => '_content.php', 'name' => '_content', 'group_name' => '<i class="kre3m kre3m-pages"></i>Vsebina', 'add_separator' => false);
    $result[] = array('caption' => 'Nastavitve', 'short_caption' => 'Nastavitve', 'filename' => '_locale.php', 'name' => '_locale', 'group_name' => '<i class="kre3m kre3m-settings"></i>Nastavitve', 'add_separator' => false);
    $result[] = array('caption' => 'Projekti', 'short_caption' => 'Projekti', 'filename' => 'project.php', 'name' => 'project', 'group_name' => '<i class="kre3m kre3m-pages"></i>Vsebina', 'add_separator' => false);
    return $result;
}

function GetPagesHeader()
{
    return
    '<h1>ZEK</h1>';
}

function GetPagesFooter()
{
    return
        ''; 
    }

function ApplyCommonPageSettings(Page $page, Grid $grid)
{
    $page->SetShowUserAuthBar(true);
    $page->OnCustomHTMLHeader->AddListener('Global_CustomHTMLHeaderHandler');
    $page->OnGetCustomTemplate->AddListener('Global_GetCustomTemplateHandler');
    $grid->BeforeUpdateRecord->AddListener('Global_BeforeUpdateHandler');
    $grid->BeforeDeleteRecord->AddListener('Global_BeforeDeleteHandler');
    $grid->BeforeInsertRecord->AddListener('Global_BeforeInsertHandler');
}

/*
  Default code page: 1250
*/
function GetAnsiEncoding() { return 'windows-1250'; }

function Global_CustomHTMLHeaderHandler($page, &$customHtmlHeaderText)
{
$customHtmlHeaderText = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
$customHtmlHeaderText.= '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script><script type="text/javascript">var jQ2 = $.noConflict(true);</script>';
$customHtmlHeaderText.= '<script src="_custom/custom.js" defer="defer2"></script>';
}

function Global_GetCustomTemplateHandler($part, $mode, &$result, &$params, Page $page = null)
{

}

function Global_BeforeUpdateHandler($page, &$rowData, &$cancel, &$message, $tableName)
{

}

function Global_BeforeDeleteHandler($page, &$rowData, &$cancel, &$message, $tableName)
{

}

function Global_BeforeInsertHandler($page, &$rowData, &$cancel, &$message, $tableName)
{

}

function GetDefaultDateFormat()
{
    return 'd.m.Y';
}

function GetFirstDayOfWeek()
{
    return 1;
}

function GetEnableLessFilesRunTimeCompilation()
{
    return false;
}



?>