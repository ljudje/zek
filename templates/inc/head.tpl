<!doctype html>
<html lang="{$lang->getCurrent()}">
<head>
	<meta charset="utf-8">
	<title>{if $page_title}{$page_title} - {elseif $page.title && !$home}{$page.title|escape} - {/}{$locale.page_title|escape}</title>
	<link href='https://fonts.googleapis.com/css?family=Roboto:300,700|Source+Sans+Pro:300,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<meta name="keywords" content="{$page_keywords}|{$locale.page_keywords|escape}" />
	<meta name="description" content="{if $page_description}{$page_description|escape}{else}{$locale.page_description|escape}{/}" />
	<meta name="author" content="Zek">
	<meta name="robots" content="all" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="google-site-verification" content="a1GyaxRQWS5rankNlw0FSnKVXXwZ8zRCwcPrEvaoZGY" />
	{$css}
	<link rel="stylesheet" type="text/css" href="/media/css/cookie.css"/>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

	<meta property="og:locale" content="{$lang->getLocale()}"/>
	{if $og_data}
		{if $locale.twitter_name}
		<meta name="twitter:card" content="summary">
		<meta name="twitter:site" content="@{$locale.tmaythedkwitter_name}">
		<meta name="twitter:creator" content="@{$locale.twitter_name}">
		{/}
		{foreach $og_data key item}
	<meta property="og:{$key}" content="{$item|escape}"/>
		{/}
	{/}

	{if $canonical}
	<link rel="canonical" href="{$canonical}" />
	{/}

	<link rel="image_src" href="/media/dsg/logo.png?3" />

	{*<link rel="apple-touch-icon" sizes="57x57" href="/media/dsg/favicon/apple-touch-icon-57x57.png">*}
	{*<link rel="apple-touch-icon" sizes="60x60" href="/media/dsg/favicon/apple-touch-icon-60x60.png">*}
	{*<link rel="apple-touch-icon" sizes="72x72" href="/media/dsg/favicon/apple-touch-icon-72x72.png">*}
	{*<link rel="apple-touch-icon" sizes="76x76" href="/media/dsg/favicon/apple-touch-icon-76x76.png">*}
	{*<link rel="apple-touch-icon" sizes="114x114" href="/media/dsg/favicon/apple-touch-icon-114x114.png">*}
	{*<link rel="apple-touch-icon" sizes="120x120" href="/media/dsg/favicon/apple-touch-icon-120x120.png">*}
	{*<link rel="icon" type="image/png" href="/media/dsg/favicon/favicon-32x32.png" sizes="32x32">*}
	{*<link rel="icon" type="image/png" href="/media/dsg/favicon/favicon-96x96.png" sizes="96x96">*}
	{*<link rel="icon" type="image/png" href="/media/dsg/favicon/favicon-16x16.png" sizes="16x16">*}
	{*<link rel="manifest" href="/media/dsg/favicon/manifest.json">*}
	{*<link rel="shortcut icon" href="/media/dsg/favicon/favicon.ico">*}
	{*<meta name="msapplication-TileColor" content="#da532c">*}
	{*<meta name="msapplication-config" content="/media/dsg/favicon/browserconfig.xml">*}
	{*<meta name="theme-color" content="#ffffff">*}

</head>
