<?
$oControle = new Controle();
$aUf = $oControle->carregarColecaoUf();
?>
<select name="<?=$nomeCampo?>" id="<?=$nomeCampo?>"<?=($evento != "") ? " $evento=\"$funcao\"" : ""?>>
	<option value="">Selecione</option>
<?
foreach($aUf as $oUf){
?>
	<option value="<?=$oUf->get_idUf();?>"><?=$oUf->get_siglaUf();?> - <?=$oUf->get_descricaoUf();?></option>
<?
}
?>
</select>