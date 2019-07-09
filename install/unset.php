<?
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILES__);

if(!check_bitrix_sessid()){

	return;
}

echo(CAdminMessage::ShowNote(Loc::getMessage("SPICHKA_IMPORTCSV_UNSTEP_BEFORE")." ".Loc::getMessage("SPICHKA_IMPORTCSV_UNSTEP_AFTER")));

?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<input type="submit" value="<?=Loc::getMessage("SPICHKA_IMPORTCSV_UNSTEP_SUBMIT_BACK")?>">
</form>