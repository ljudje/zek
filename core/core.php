<?php

// Start session
session_start();

//	Prevent session fixation
if ($_SERVER['REMOTE_ADDR'] !== @$_SESSION['PREV_REMOTEADDR'] && substr($_SERVER['HTTP_USER_AGENT'], 0, 60) !== substr(@$_SESSION['PREV_USERAGENT'], 0 ,60)) {
	session_destroy();
	session_start();
}
session_regenerate_id();
$_SESSION['PREV_USERAGENT'] = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['PREV_REMOTEADDR'] = $_SERVER['REMOTE_ADDR'];

//	Debug
include("system/debug.php");

//	Locad config
include("system/config.php");	//

include("system/autoload.php");	//

//	Load model
include("system/model.php");	//

//	Load project
include("system/project.php");
include("system/project_model.php");

//	Load wireframe
include("system/wireframe.php");

//	Load exception
include("system/exception.php");	//

//	Load module
include("system/module.php");

//	Load params
include("system/params.php");	//

//	Load locate settings
include("system/locale.php");

//	Load locate settings
include("system/language.php");	//

//	Load page structure
include("system/page.php");

//	Load user
include("system/user.php");

//	Load templates
include("lib/dwoo/dwooAutoload.php");
include("system/template.php");

// Error handler
//include("system/error_handler.php");

//	Load database class
include("system/db.php");	//

//	Load forms
include("lib/formhandler/class.FormHandler.php");
define( 'FH_EXPOSE', false );

//	Load mailer
include("lib/phpmailer/class.phpmailer.php");
