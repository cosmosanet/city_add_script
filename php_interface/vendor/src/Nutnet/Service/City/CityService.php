<?
namespace Nutnet\Service\City;

use Bitrix\Iblock\IblockTable;
use CIBlockElement;
use CIBlockResult;
use CUtil;

class CityService
{
    public function fileToArray($filePath)
    {
        $citysFromFile = explode("\n", file_get_contents('resources/city.txt'));

        return $citysFromFile;
    }

    public function getIblockElements(string $iBlockCode, array $arFilter)
    {
        $iBlockID = $this->getIdForIblockCode($iBlockCode);
        $elementFromIblock = CIBlockElement::GetList(
            [

            ],
            [
                'IBLOCK_ID' => $iBlockID,
            ],
            false,
            false,
            $arFilter,
        );

        return $elementFromIblock;
    }

    public function getIdForName(string $name): int
    {
        $cityId = CIBlockElement::GetList(
            [],
            [
                'NAME' => $name,
            ],
            false,
            false,
            [
                'ID',
            ]
        )->Fetch()['ID'];

        return $cityId;
    }

    public function setPropertyValues (int $cityId, string $iBlockCode, array $values): void
    {
        $iBlockID = $this->getIdForIblockCode($iBlockCode);
        CIBlockElement::SetPropertyValuesEx(
            $cityId,
            $iBlockID ,
            $values,
        );
    }

    public function getIBlockElementsToArrayNames(CIBlockResult $cIBlockResult): array
    {
        $citysNameFromIblock = [];
        array_push($citysNameFromIblock, $cIBlockResult->Fetch()['NAME']);
        while ($arItem = $cIBlockResult->GetNext()) {
            array_push($citysNameFromIblock, $arItem['NAME']);
        }
        // var_dump(array_search('Абдулино', $citysNameFromIblock));
        return $citysNameFromIblock;
    }

    public function addElement(string $code, string $elementName, array $values): void
    {
        $iblockId = $this->getIdForIblockCode($code);
        $el = new CIBlockElement; 
            $el->Add(
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => $elementName,
                    'CODE' =>  CUtil::translit($elementName, 'ru'),
                    'PROPERTY_VALUES' => $values
                ]
            );
    }

    private function getIdForIblockCode(string $code): int
    {
        $iblockId = intval(IblockTable::getList(
            [
                'filter' => [
                    'CODE' => $code,
                ],
                'select' => [
                    'ID',
                    'CODE',
                ],
            ]
        )->fetch()['ID']);
        return $iblockId;
    }

    public function randPhone(): string
    {
        $num = '8';
        for ($i = 0; $i <= 9; $i++) {
            $num .= mt_rand(0, 9);
        }
        return $num;
    }
}