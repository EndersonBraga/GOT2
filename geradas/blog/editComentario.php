<?php
require_once("classes/class.Controle.php");
$oControle = new Controle();
$oComentario = $oControle->selecionarComentario($_REQUEST['idComentario']);
 
// ================= Edicao do Comentario ========================= 
if($_POST){
	print ($oControle->alteraComentario()) ? "" : $oControle->msg; exit;
}
$aPost = $oControle->carregarColecaoPost();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php require_once("includes/header.php");?>
</head>
<body>
    <?php require_once("includes/head.php");?>
    <div class="container">
        <?php 
        require_once("includes/titulo.php"); 
        require_once("includes/menu.php"); 
        ?>
        <ol class="breadcrumb">
            <li><a href="principal.php">Home</a></li>
            <li><a href="admComentario.php">Comentario</a></li>
            <li class="active">Editar <span>Comentario</span></li>
        </ol>
<?php 
if($oControle->msg != "")
    $oControle->componenteMsg($oControle->msg, "erro");
?>
        <form role="form" onsubmit="return false;">

<div class="form-group">
    <label for="idPost">Post</label>
    <select name="idPost" id="idPost" class="form-control">
        <option value="">Selecione</option>
    <?php
    foreach($aPost as $oPost){
    ?>
        <option value="<?=$oPost->idPost?>"<?=($oPost->idPost == $oComentario->oPost->idPost) ? " selected" : ""?>><?=$oPost->titulo?></option>
    <?php
    }
    ?>
    </select>
</div>
<div class="form-group">
    <label for="descricao">Descricao</label>
    <textarea name="descricao" class="form-control" id="descricao" cols="80" rows="10"><?=$oComentario->descricao?></textarea>
</div>
<div class="form-group">
    <label for="nome">Nome</label>
    <input type="text" class="form-control" id="nome" name="nome" value="<?=$oComentario->nome?>" />
</div>
<div class="form-group">
    <label for="email">email</label>
    <div class="input-group">
        <div class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></div>
        <input type="email" class="form-control" name="email" id="email" value="<?=$oComentario->email?>" />
    </div>
</div>
<div class="form-group">
    <label for="webpage">webpage</label>
    <div class="input-group">
        <div class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></div>
        <input type="url" class="form-control" name="webpage" id="webpage" value="<?=$oComentario->webpage?>" />
    </div>
</div>

                            <label for="dataHoraCadastro">DataHoraCadastro</label>
                            <?php $oControle->componenteCalendario('dataHoraCadastro', Util::formataDataHoraBancoForm($oComentario->dataHoraCadastro), NULL, true)?>
            <div class="form-actions">
                <button id="btnEditar" data-loading-text="loading..." type="submit" class="btn btn-primary">Salvar</button>
                <a class="btn btn-default" href="admComentario.php">Voltar</a>
                <input name="idComentario" type="hidden" id="idComentario" value="<?=$_REQUEST['idComentario']?>" />
            </div>
        </form>
    </div>
    <?php require_once("includes/footer.php")?>
</body>
</html>