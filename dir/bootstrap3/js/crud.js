$(document).ready(function(){
    var classe = $(".active > span").html();

    /**
     * 
     * Ação Cadastrar
     * 
     * @author luizleao
     */
    $("#btnCadastrar").click(function () {
        dados = retornaParametros(document.forms[0]);
        $.ajax({
            url : 'cad'+classe+'.php',
            type : 'post',
            data : dados,
            dataType: 'html',
            beforeSend: function(){
                $('#btnCadastrar').button('loading');
            },
            timeout: 3000,
            success: function(retorno){
                $('#btnCadastrar').button('reset');

                if(retorno !== '')
                    $('#modalResposta > .modal-body').html('<img src="img/ico_error.png" /> '+retorno);
                else
                    $('#modalResposta > .modal-body').html('<img src="img/ico_success.png" />Cadastrado com sucesso');
                $('#modalResposta').modal('show');
            },
            error: function(retorno){
                $('#btnCadastrar').button('reset');
                $('#modalResposta > .modal-body').html('<img src="img/ico_error.png" /> '+retorno);
                $('#modalResposta').modal('show');
            }
        });
        $('#modalResposta').on('shown', function (){
            $('#modalResposta > .modal-footer > #btnFechar').focus();
        });
    });

    /**
     * 
     * Ação Editar
     * @author luizleao
     */
    $("#btnEditar").click(function () {
        dados = retornaParametros(document.forms[0]);
        $.ajax({
            url : 'edit'+classe+'.php',
            type : 'post',
            data : dados,
            dataType: 'html',
            beforeSend: function(){
                $('#btnEditar').button('loading');
            },
            timeout: 3000,
            success: function(retorno){
                $('#btnEditar').button('reset');

                if(retorno !== '')
                    $('#modalResposta > .modal-body').html('<img src="img/ico_error.png" /> '+retorno);
                else
                    $('#modalResposta > .modal-body').html('<img src="img/ico_success.png" /> Editado com sucesso');
                $('#modalResposta').modal('show');
            },
            error: function(){
                $('#btnEditar').button('reset');
                $('#modalResposta > .modal-body').html('Erro!!');
                $('#modalResposta').modal('show');
            }
        });
        $('#modalResposta').on('shown', function (){
            $('#modalResposta > .modal-footer > #btnFechar').focus();
        });
    });
	
    /**
     * 
     * Ação Logar
     * @author luizleao
     */
    $('#btnLogar').click(function(){
        dados = retornaParametros(document.forms[0]);
        $.ajax({
            url : 'resIndex.php',
            type : 'post',
            data : dados,
            dataType: 'html',
            beforeSend: function(){
                $('#btnLogar').button('loading');
            },
            timeout: 3000,
            success: function(retorno){
                $('#btnLogar').button('reset');

                if(retorno !== ''){
                    $('#modalResposta > .modal-body').html('<img src="img/ico_error.png" /> '+retorno);
                    $('#modalResposta').modal('show');
                } else{
                    window.location = 'principal.php';
                }
            },
            error: function(retorno){
                $('#btnLogar').button('reset');
                $('#modalResposta > .modal-body').html('<img src="img/ico_error.png" /> Erro: '+retorno);
                $('#modalResposta').modal('show');
            }
        });
        $('#modalResposta').on('shown', function (){
            $('#modalResposta > .modal-footer > #btnFechar').focus();
        });
    });
	
    /**
     * 
     * Cadastrar programas ao grupo selecionado
     * @author luizleao
     */
    $('#btnCadastroPrograma').click(function (){
        dados = retornaParametros(document.forms[0]);
        $.ajax({
            url : 'cadGrupoPrograma.php',
            type : 'post',
            data : dados,
            dataType: 'html',
            beforeSend: function(){
                $('#btnCadastroPrograma').button('loading');
            },
            timeout: 3000,
            success: function(retorno){
                $('#btnCadastroPrograma').button('reset');

                if(retorno !== '')
                    $('#modalResposta > .modal-body').html('<img src="img/ico_error.png" /> '+retorno);
                else
                    $('#modalResposta > .modal-body').html('<img src="img/ico_success.png" />Cadastrado com sucesso');
                $('#modalResposta').modal('show');
            },
            error: function(retorno){
                $('#btnCadastroPrograma').button('reset');
                $('#modalResposta > .modal-body').html('<img src="img/ico_error.png" /> '+retorno);
                $('#modalResposta').modal('show');
            }
        });
        $('#modalResposta').on('shown', function (){
            $('#modalResposta > .modal-footer > #btnFechar').focus();
        });
    });
    
    // Mascaramento de dados
    $('.date').mask('11/11/1111');
    $('.time').mask('00:00:00');
    $('.date_time').mask('00/00/0000 00:00:00');
    $('.cep').mask('00000-000');
    $('.telefone').mask('(00) 0000-0000');
    $('.celular').mask('(00) 00000-0000');
    $('.mixed').mask('AAA 000-S0S');
    $('.cpf').mask('000.000.000-00', {reverse: true});
    $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
    $('.money').mask('000.000.000.000.000,00', {reverse: true});
});

function excluir(campo, valor){
    var classe = $(".active > span").html();

    $('#modalExcluir').modal('show');
    $('#modalExcluir > .modal-body').html('Deseja excluir '+ classe +'?');

    $('#btnSim').click(function () {
        $.ajax({
            url  	   : 'adm'+classe+'.php?acao=excluir&'+campo+'='+valor,
            type 	   : 'get',
            beforeSend : function(){
                    $('#btnCadastrar').button('loading');
            },
            timeout	   : 3000,
            success	   : function(retorno){
                $('#modalExcluir').modal('hide');
                if(retorno !== ''){
                    $('#modalResposta > .modal-body').html('<img src="img/ico_error.png" /> '+retorno);
                    $('#modalResposta').modal('show');
                }
                else{
                    $('#modalResposta > .modal-body').html('<img src="img/ico_success.png" /> Excluido com sucesso');
                    $('#modalResposta').modal('show');
                    $('#modalResposta').on('hide', function () {
                        window.location = 'adm'+classe+'.php';
                    });
                }
            },
            error	   : function(retorno){
                $('#modalExcluir').modal('hide');
                $('#modalResposta > .modal-body').html('<img src="img/ico_error.png" /> ERRO: '+retorno);
                $('#modalResposta').modal('show');
            }
        });
    });
}