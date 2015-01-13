<?php
/**
 * Interface de implementação de classes de conexão para qualquer SGBD
 * 
 * @author Luiz Leão <luizleao@gmail.com>
 */
interface IConexao{
    /**
     * Retorna a lista de tabelas de database
     * 
     */
    function carregarColecaoTabelas();
    
    /**
     * Retorna a lista de colunas de uma tabela
     * 
     * @param string $tabela
     */
    function carregarColecaoColunasTabela($tabela);
    
    /**
     * Retorna os dados da chaves estrangeiras da coluna
     * 
     * @param string $db
     * @param string $tabela
     * @param string $coluna
     */
    function dadosForeignKeyColuna($db, $tabela, $coluna);
}

