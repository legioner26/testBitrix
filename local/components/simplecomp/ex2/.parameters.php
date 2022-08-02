<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if( !Loader::includeModule("iblock") ) {
    throw new \Exception('Не загружены модули необходимые для работы компонента');
}

$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(

        "IBLOCK_CATALOG_ID" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("EX2_IBLOCK_CATALOG_ID"),
        ),

        "IBLOCK_NEWS_ID" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("EX2_IBLOCK_NEWS_ID"),
        ),

        "USER_PROPERTY" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("EX2_USER_PROPERTY"),
        ),

        "CACHE_TIME" => Array("DEFAULT" => 3600),
    ),
);