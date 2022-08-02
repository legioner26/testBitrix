<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use \Bitrix\Iblock;
use \Bitrix\Main\Loader;
use \Bitrix\Iblock\SectionTable;
use \Bitrix\Iblock\Model\Section;

class Simplecomp extends CBitrixComponent
{
    /**
     * Подготавливаем параметры
     *
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {

        if (!$arParams["CACHE_TIME"]) {
            $arParams["CACHE_TIME"] = 3600;
        }

        return $arParams;
    }

    public function setArResult()
    {
        Loader::includeModule("iblock");
        $arFilter = [
            "IBLOCK_ID" => $this->arParams["IBLOCK_CATALOG_ID"],
            "ACTIVE" => "Y",
            "!" . $this->arParams["USER_PROPERTY"] => false,
        ];

        $arSelect = [
            "ID",
            "IBLOCK_ID",
            "NAME",
            $this->arParams["USER_PROPERTY"],
        ];
        $entity = Section::compileEntityByIblock($this->arParams["IBLOCK_CATALOG_ID"]);
        $section = $entity::getList([
            "select" => $arSelect,
            "filter" => $arFilter
        ]);
        while ($sectionOb = $section->fetch()) {
            $arProductSectionsTotal[] = $sectionOb;

        }

        $arProductSectionsId = array_column($arProductSectionsTotal, "ID");

        // <Выборка товаров из ИБ "Продукция" по выбранным разделам>

       $class =  \Bitrix\Iblock\Iblock::wakeUp($this->arParams["IBLOCK_CATALOG_ID"])->getEntityDataClass();

        $products = $class::getList([
            'select' => ['ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'ARTNUMBER_' => 'ARTNUMBER', 'MATERIAL_'=>'MATERIAL', 'PRICE_'=>'PRICE'],
            'filter' => ['=ACTIVE' => 'Y','IBLOCK_ID' => $this->arParams['IBLOCK_CATALOG_ID'], 'IBLOCK_SECTION_ID' => $arProductSectionsId],
        ])->fetchAll();
        $iCount = 0;
        $arAllPrice = [];
        foreach ($products as $product) {
            $arAllPrice[] = $product["PRICE_IBLOCK_GENERIC_VALUE"];

            foreach ($arProductSectionsTotal as &$arSection) {
                if ($product["IBLOCK_SECTION_ID"] == $arSection["ID"]) {

                    $arButtons = CIBlock::GetPanelButtons(
                        $product["IBLOCK_ID"],
                        $product["ID"],
                        0,
                        ["SECTION_BUTTONS" => false, "SESSID" => false]
                    );
                    $product["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                    $product["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

                    $arSection["ITEMS"][] = $product;
                }
            }
           
            $iCount++;
        }

        // Количество товаров
        $this->arResult["COUNT"] = $iCount;

        $class =  \Bitrix\Iblock\Iblock::wakeUp($this->arParams["IBLOCK_NEWS_ID"])->getEntityDataClass();

        $res = $class::getList([
            'select' => ['ID', 'NAME', 'IBLOCK_ID', 'ACTIVE_FROM'],
            'filter' => ['=ACTIVE' => 'Y','IBLOCK_ID' => $this->arParams["IBLOCK_NEWS_ID"]],
        ])->fetchAll();
        $i = 0;
        foreach ($res as $arRes) {
            $this->arResult["ITEMS"][$i] = $arRes;
            foreach ($arProductSectionsTotal as &$arSection2) {

                foreach ($arSection2[$this->arParams["USER_PROPERTY"]] as $item) {
                    if ($item == $arRes["ID"]) {
                        $this->arResult["ITEMS"][$i]["ITEMS"][] = $arSection2;
                    }
                }
            }
            $i++;
        }

//echo '<pre>'; print_r( $this->arResult); echo '</pre>';

    }

    /**
     * @return mixed|void
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {
        global $APPLICATION;
        global $USER;

        if (!Loader::includeModule("iblock")) {
            ShowError(GetMessage("EX2_IB_CHECK"));
            return;
        }


        if ($this->StartResultCache()) {
            $this->setArResult();
            // Список ключей массива $arResult, которые должны кэшироваться при использовании встроенного кэширования компонентов, иначе закеширует весь массив arResult, кэш сильно разростается
            $this->setResultCacheKeys(["COUNT"]);
            $this->includeComponentTemplate();
        }

        $APPLICATION->SetTitle(GetMessage("EX2_ELEMENTS_COUNT") . $this->arResult["COUNT"]);


        if (
            !empty($this->arParams["IBLOCK_CATALOG_ID"]) &&
            $USER->IsAuthorized() &&
            // Возвращает "true", если кнопка "Показать включаемые области" на панели управления нажата, в противном случае - "false".
            $APPLICATION->GetShowIncludeAreas()
        ) {
            // Метод возвращает массив, описывающий набор кнопок для управления элементами инфоблока
            $arButtons = CIBlock::GetPanelButtons(
                $this->arParams["IBLOCK_CATALOG_ID"],
                0,
                0,
                ["SECTION_BUTTONS" => false]
            );
            // Добавляет массив новых кнопок к тем кнопкам компонента, которые отображаются в области компонента в режиме редактирования сайта.
            $this->addIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
        }

    }
}