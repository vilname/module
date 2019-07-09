<?
namespace Spichka\Importcsv;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Loader;

Loader::includeModule("iblock");


class Main
{
	public $sectionData;
	public $arResPars = array();
	public $arElem = array();
	public $arSectionCheck2Lvl = array();
	
	public function getSectionSite($idIblock){
		
	  	$arFilter = Array('IBLOCK_ID'=>$idIblock, 'ACTIVE'=>'Y', 'DEPTH_LEVEL'=>1);
	  	$db_list = \CIBlockSection::GetList(Array('NAME'=>'ASC'), $arFilter, true);

	  	while($ar_result = $db_list->GetNext())
	  	{
	    	$this->sectionData[] = $ar_result;
	  	}

	  	return $this->sectionData;
	}

	public function getParseDocument($urlfile){
		$arSectionRes = array();
		$arData = array();
		$arSection = array();

		$row = 0;
		if (($handle = fopen($urlfile, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
		        $num = count($data);
		       
		        for ($c=0; $c < $num; $c++) {
		            $arData[$row][] = iconv('CP1251', 'UTF-8', $data[$c]);
		        }
		        $row++;
		    }
		    fclose($handle);
		}


		foreach ($arData as $key => $value) {
			if($key === 0){
				foreach ($value as $k => $v) {
					$arData[$key][$k] = trim($v);
				}
				
			}
		}

		
		foreach ($arData as $key => $value) {
			if($key !== 0){
				foreach ($value as $k => $v) {
					$this->arResPars["ITEMS"][$key][$arData[0][$k]] = trim($v);
				}
			}
		}
		unset($arData);




		foreach ($this->arResPars["ITEMS"] as $key => $value) {

			if($value["марка"]){
				$arSection[] = $value["марка"];
			}

			if(explode(",", $value["Розница"])[0] != ""){
				$this->arResPars["ITEMS"][$key]["Розница"] = explode(",", $value["Розница"])[0];
			}
		}

		$arSectionRes = array_unique($arSection);
		$this->arResPars["SECTION"] = $arSectionRes;

		return $this->arResPars;
	}

	public function getDataIblock($idIblock, $idSection, $manufacturer){
		$arSelect = Array("ID", "NAME", "IBLOCK_SECTION_ID", "PROPERTY_PRODUCER", "PROPERTY_VENDOR_CODE", "PROPERTY_MODEL", "PROPERTY_YEAR", "PROPERTY_NOMENCLATURE");
		$arFilter = Array("IBLOCK_ID"=>IntVal($idIblock), "ACTIVE"=>"Y", "SECTION_ID"=>$idSection, "PROPERTY_PRODUCER"=>$manufacturer);
		$res = \CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
		while($ob = $res->GetNextElement())
		{
		 	$arFields = $ob->GetFields();
		 
		 	$this->arElem[$arFields["PROPERTY_VENDOR_CODE_VALUE"]]["ID"] = $arFields["ID"];
		 	$this->arElem[$arFields["PROPERTY_VENDOR_CODE_VALUE"]]["NAME"] = $arFields["NAME"];
		 	$this->arElem[$arFields["PROPERTY_VENDOR_CODE_VALUE"]]["SECTION_ID"] = $arFields["IBLOCK_SECTION_ID"];
		 	$this->arElem[$arFields["PROPERTY_VENDOR_CODE_VALUE"]]["PRODUCER"] = $arFields["PROPERTY_PRODUCER_VALUE"];
		 	$this->arElem[$arFields["PROPERTY_VENDOR_CODE_VALUE"]]["VENDOR_CODE"] = $arFields["PROPERTY_VENDOR_CODE_VALUE"];
		 	$this->arElem[$arFields["PROPERTY_VENDOR_CODE_VALUE"]]["MODEL"] = $arFields["PROPERTY_MODEL_VALUE"];
		 	$this->arElem[$arFields["PROPERTY_VENDOR_CODE_VALUE"]]["YEAR"] = $arFields["PROPERTY_YEAR_VALUE"];
		 	$this->arElem[$arFields["PROPERTY_VENDOR_CODE_VALUE"]]["NOMENCLATURE"] = $arFields["PROPERTY_NOMENCLATURE_VALUE"];

		}
		return $this->arElem;
	}

	public function getSectionIblock($idIblock, $idSection){

		$arSelect = array("ID", "NAME");
		$arFilter = Array('IBLOCK_ID'=>$idIblock, 'ACTIVE'=>'Y', 'SECTION_ID'=>$idSection, 'DEPTH_LEVEL'=>2);
	  	$db_list = \CIBlockSection::GetList(Array(), $arFilter, false, $arSelect);

	  	while($ar_result = $db_list->GetNext())
	  	{
	    	$this->arSectionCheck2Lvl[$ar_result["ID"]] = $ar_result["NAME"];
	  	}

		return $this->arSectionCheck2Lvl;
	}

	public function setCheckSection($secFile, $secSite, $idIblock, $parentSection){

		foreach ($secFile as $key => $value) {
			foreach ($secSite as $k => $v) {
				if($value == $v){
					unset($secFile[$key]);
				}
			}
		}

		$bs = new \CIBlockSection;

		$arParams = array("replace_space"=>"-","replace_other"=>"-");



		foreach ($secFile as $key => $value) {
			$arFields = Array(
				"IBLOCK_ID" => $idIblock,
				"NAME" => $value,
				"CODE" => \Cutil::translit($value,"ru",$arParams),
				"IBLOCK_SECTION_ID" => $parentSection
			);

			$bs->Add($arFields);

		}

		return true;

	}
	public function setSheckElement($secFile, $elemSite, $idIblock, $parentSection, $sectionSiteData){

		// $newElem = array();
		// $PROP = array();
	
		// foreach ($secFile as $key => $value) {
		// 	if(!$elemSite[$value["Артикул"]]){
		// 		$newElem[$key] = $value;
		// 	}else{
		// 		$availableElem[$key] = $value;
		// 	}
		// }

		// $el = new CIBlockElement;

		// $arParams = array("replace_space"=>"-","replace_other"=>"-");



		// foreach ($newElem as $key => $value) {
					
		// 	$PROP["PROPERTY_PRODUCER"] = $value["Производитель"];
		// 	$PROP["PROPERTY_VENDOR_CODE"] = $value["Артикул"]; 
		// 	$PROP["PROPERTY_MODEL"] = $value["MODEL"]; 
		// 	$PROP["PROPERTY_YEAR"] = $value["YEAR"];
		// 	$PROP["PROPERTY_NOMENCLATURE"] = $value["NOMENCLATURE"];  

		// 	$arLoadProductArray = Array(        // элемент лежит в корне раздела
		// 	  "IBLOCK_ID"      => $idIblock,
		// 	  "PROPERTY_VALUES"=> $PROP,
		// 	  "NAME"           => $value,
		// 	  "IBLOCK_SECTION_ID" => "",
		// 	  "CODE"           => \Cutil::translit("Элемент","ru",$arParams)
		// 	  );

		// 	// $PRODUCT_ID = $el->Add($arLoadProductArray);
		// }




	}
}


?>