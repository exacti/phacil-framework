<?php
if (version_compare(phpversion(), '7.0.0', '>') == false) {
    define('TwigFolderLoad', 'Twig1x');
    define('TwigLoaderFilesystem', 'Twig_Loader_Filesystem');
    define('TwigEnvironment', 'Twig_Environment');
    define('TwigSimpleFilter', 'Twig_SimpleFilter');
    define('TwigExtensionDebug', 'Twig_Extension_Debug');

} else {
    define('TwigLoaderFilesystem', '\Twig\Loader\FilesystemLoader');
    define('TwigEnvironment', '\Twig\Environment');
    define('TwigSimpleFilter', '\Twig\TwigFilter');
    define('TwigExtensionDebug', '\Twig\Extension\DebugExtension');

    if (version_compare(phpversion(), '7.2.0', '>') == false) {
        define('TwigFolderLoad', 'Twig2x');
    } else {
        define('TwigFolderLoad', 'Twig3x');
    }
}

include_once TwigFolderLoad."/autoload.php";


if(TwigFolderLoad == 'Twig1x') {
    if (file_exists(__DIR__ . "/Extension/ExacTITranslate1x.php")) include __DIR__ . "/Extension/ExacTITranslate1x.php";
} else {
    if (file_exists(__DIR__ . "/Extension/ExacTITranslate.php")) include __DIR__ . "/Extension/ExacTITranslate.php";
}