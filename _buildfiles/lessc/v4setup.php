#!/usr/bin/php
<?php

echo "compiling skin css files...\n";

//files to compile
include __DIR__.'/lessc.inc.php';

$less = new lessc;

$arrFilesToCompile = array(
    __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/less/bootstrap.less" => __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/less/styles.css",
    __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/less/responsive.less" => __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/less/responsive.css",
    __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/less/bootstrap_pe.less" => __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/less/styles_pe.css",


    __DIR__."/../temp/kajona/core/module_installer/less/bootstrap.less" => __DIR__."/../temp/kajona/core/module_installer/less/styles.css",
    __DIR__."/../temp/kajona/core/module_installer/less/responsive.less" => __DIR__."/../temp/kajona/core/module_installer/less/responsive.css",
);

$strSkinReplacement = "";
$strSkinReplacement = <<<TXT
    <link rel="stylesheet" href="_skinwebpath_/less/styles.css?_system_browser_cachebuster_" type="text/css" />
    <link rel="stylesheet" href="_skinwebpath_/less/responsive.css?_system_browser_cachebuster_" type="text/css" />
TXT;

$strInstallerReplacement = "";
$strInstallerReplacement = <<<TXT
    <link rel="stylesheet" href="_webpath_/core/module_installer/less/styles.css?_system_browser_cachebuster_" type="text/css" />
    <link rel="stylesheet" href="__webpath_/core/module_installer/less/responsive.css?_system_browser_cachebuster_" type="text/css" />
TXT;


$strPeReplacement = "";
$strPeReplacement = <<<TXT
    <link rel="stylesheet" href="_skinwebpath_/less/styles_pe.css?_system_browser_cachebuster_" type="text/css" />
TXT;

$arrFilesToUpdate = array(
    __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/main.tpl" => $strSkinReplacement,
    __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/folderview.tpl" => $strSkinReplacement,
    __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/login.tpl" => $strSkinReplacement,
    __DIR__."/../temp/kajona/core/module_v4skin/admin/skins/kajona_v4/elements.tpl" => $strPeReplacement,
    __DIR__."/../temp/kajona/core/module_installer/installer.tpl" => $strInstallerReplacement
);


foreach($arrFilesToCompile as $strSourceFile => $strTargetFile) {
    $strLessFile = $less->compileFile($strSourceFile);
    file_put_contents($strTargetFile, $strLessFile);
}

echo "merging into skin-files...\n";
$strStartPlaceholder = "<!-- KAJONA_BUILD_LESS_START -->";
$strEndPlaceholder = "<!-- KAJONA_BUILD_LESS_END -->";

foreach($arrFilesToUpdate as $strOneFile => $strReplacement) {
    $strContent = file_get_contents($strOneFile);
    $strPrologue = substr($strContent, 0, strpos($strContent, $strStartPlaceholder));
    $strEnd = substr($strContent, strpos($strContent, $strEndPlaceholder)+strlen($strEndPlaceholder));
    $strContent = $strPrologue.$strReplacement.$strEnd;
    file_put_contents($strOneFile, $strContent);
}
