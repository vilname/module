<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::includeModule('spichka.importcsv');

use Spichka\Importcsv\Main;

$url = $_POST["url_main_file"];

header( 'Location:' . $url );


$idIblock = 107;

$text = $_FILES["file_data"]["tmp_name"];
$idSection = $_POST["section"];

$manufacturer = '';

$fileOb = new Main;
$fileData = $fileOb->getParseDocument($text);  /// получаю данные из файла csv

foreach ($fileData["ITEMS"] as $key => $value) {
	if($value["Производитель"] && isset($manufacturer)){
		$manufacturer = $value["Производитель"];
	}
}



$elemSiteData = $fileOb->getDataIblock($idIblock, $idSection, $manufacturer); /// получаю элементы инфоблока
$sectionSiteData = $fileOb->getSectionIblock($idIblock, $idSection); /// получаю разделы инфоблока

// $fileOb->setCheckSection($fileData["SECTION"], $sectionSiteData, $idIblock, $idSection); /// записываю новые разделы
// $fileOb->setSheckElement($fileData["ITEMS"], $elemSiteData, $idIblock, $idSection, $sectionSiteData);





// $row = 1;
// if (($handle = fopen($file_data, "r")) !== FALSE) {
//     while (($data = fgetcsv($file_data, 1000, ",")) !== FALSE) {
//         $num = count($data);
//         // echo "<p> $num fields in line $row: <br /></p>\n";
//         $row++;
//         for ($c=0; $c < $num; $c++) {
        	


//             //echo $data[$c] . "<br />\n";
//         }
//     }
//     fclose($handle);
// }



?>