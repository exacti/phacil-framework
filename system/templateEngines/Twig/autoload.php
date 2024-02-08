<?php
namespace {
    $preferenceDIObj = 'Phacil\Framework\templateEngines\Twig\Api\Extension\TranslateInterface';
    if (version_compare(phpversion(), '7.2.5', '>') == false) {
        define('TwigFolderLoad', 'Twig1x');
        define('TwigLoaderFilesystem', 'Twig_Loader_Filesystem');
        define('TwigEnvironment', 'Twig_Environment');
        define('TwigSimpleFilter', 'Twig_SimpleFilter');
        define('TwigExtensionDebug', 'Twig_Extension_Debug');

        if(!\Phacil\Framework\Registry::checkPreferenceExist($preferenceDIObj)){
            \Phacil\Framework\Registry::addDIPreference($preferenceDIObj, "Phacil\\Framework\\templateEngines\\Twig\\Extension\\Legacy\\Translate");
        }
    } else {
        define('TwigLoaderFilesystem', '\Twig\Loader\FilesystemLoader');
        define('TwigEnvironment', '\Twig\Environment');
        define('TwigSimpleFilter', '\Twig\TwigFilter');
        define('TwigExtensionDebug', '\Twig\Extension\DebugExtension');

        if (!\Phacil\Framework\Registry::checkPreferenceExist($preferenceDIObj)) {
            \Phacil\Framework\Registry::addDIPreference($preferenceDIObj,  'Phacil\Framework\templateEngines\Twig\Extension\Translate');
        }
    }

    if(defined('TwigFolderLoad')){
        include_once TwigFolderLoad."/vendor/autoload.php";
    }
}