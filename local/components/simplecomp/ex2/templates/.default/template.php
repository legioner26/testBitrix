<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<b>
    <?= GetMessage("EX2_CATALOG") ?>
</b>
<ul>
    <? foreach ($arResult["ITEMS"] as $arItem): ?>

        <div>
            <li>
                <b><?= $arItem["NAME"] ?></b> - <?= $arItem["DATE_ACTIVE_FROM"] ?>
                <!-- Вывод секций в скобках -->
                <? if (!empty($arItem["ITEMS"])): ?>
                    (
                    <? foreach ($arItem["ITEMS"] as $iKey => $arSection): ?>
                        <? $sComma = ""; ?>
                        <? if ($iKey != array_pop(array_keys($arItem["ITEMS"]))): ?>
                            <? $sComma = ","; ?>
                        <? endif; ?>
                        <?= $arSection["NAME"] . $sComma ?>
                    <? endforeach; ?>
                    )
                <? endif; ?>
                <!-- Вывод товаров по пунктам -->
                <ul>
                    <? foreach ($arItem["ITEMS"] as $iKey => $arSection): ?>
                        <? foreach ($arSection["ITEMS"] as $arElement): ?>
                            <?
                            $sElementId = $arItem["ID"] . $arSection["ID"] . $arElement["ID"];

                            $this->AddEditAction($sElementId, $arElement["EDIT_LINK"],
                                CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_EDIT"));
                            $this->AddDeleteAction($sElementId, $arElement["DELETE_LINK"],
                                CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_DELETE"),
                                array("CONFIRM" => GetMessage("EX2_ELEMENT_DELETE_CONFIRM")));
                            ?>

                            <div id="<?= $this->GetEditAreaId($sElementId); ?>">
                                <li>
                                    <?= $arElement["NAME"] ?> -
                                    <?= $arElement["PRICE_IBLOCK_GENERIC_VALUE"] ?> -
                                    <?= $arElement["MATERIAL_VALUE"] ?> -
                                    <?= $arElement["ARTNUMBER_VALUE"] ?>
                                </li>
                            </div>
                        <? endforeach; ?>
                    <? endforeach; ?>
                </ul>
            </li>
        </div>
    <? endforeach; ?>
</ul>