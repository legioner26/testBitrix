<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;
$arComponentDescription = [
    "NAME"        =>  Loc::getMessage("EX2_NAME"),
    "DESCRIPTION" =>  Loc::getMessage("EX2_NAME"),
    "CACHE_PATH"  => "Y",
    "SORT"        => 1,
    "PATH"        => [
        "ID"   => "exam",
        "NAME" =>  Loc::getMessage("EX2_PATH_NAME"),
    ],
];