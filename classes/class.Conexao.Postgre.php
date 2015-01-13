<?php
/** 
* Classe de conexão nativa com o SGBD PostgreSQL
* 
* @author Luiz Leão
*/
class ConexaoPostgre implements IConexao {
    /**
     * Dados da conexão
     * 
     * @var resource
     */
    public $conexao;
    /**
     * Dados da consulta
     * 
     * @var resource
     */
    public $consulta;  
    /**
     * Mensagem do sistema
     * 
     * @var string 
     */
    public $msg;
    
    /**
     * Método construtor da classe
     * 
     * @param string $servidor Servidor a ser utilizado
     * @return void
     */
    function __construct($servidor = 'Producao'){
        switch ($servidor){
            case 'Local':
                $this->set_conexao("192.168.200.25","apsuser","seduc@control","seduc_desenv");
            break;
            case 'Producao':				
                $this->set_conexao("192.168.200.42","apsuser","seduc@control","seduc_oficial");
            break;
            case 'Vazia':
            break;
            default:
                    die("Servidor $servidor inexistente");
            break;
        }
    }
    
    /**
     * Seleciona a conexão com o SGBD
     * 
     * @param string $host Endereço do servidor
     * @param string $user Usuário do banco
     * @param string $senha Senha do banco
     * @param string $bd Banco de dados selecionado
     * @return void
     */
    function set_conexao($host,$user,$senha,$bd){
        $this->conexao = @pg_connect("host=$host dbname=$bd user=$user password=$senha") or die (pg_last_error());
    }
    
    /**
     * Executa uma consulta do SGBD
     * 
     * @param string $sql
     * @return boolean
     */
    function execute($sql){
        $consulta = @pg_query($this->conexao,$sql);
        if($consulta){
            $this->consulta = $consulta;
            return true;
        } else{
            $this->msg = pg_last_error();
            return false;
        }
    }
    
    /**
     * Retorna a quantidades de linhas afetadas pela Query
     * 
     * @param resource $consulta Consulta executada
     * @return int
     */
    function numRows($consulta = NULL){
        if(!$consulta) $consulta = $this->consulta;
        return (int) @pg_num_rows($consulta);
    }
    
    /**
     * Retorna os dados da consulta em forma de array
     * 
     * @param resource $consulta
     * @return string[]
     */
    function fetchReg($consulta = NULL){
        if(!$consulta) $consulta = $this->consulta;
        return pg_fetch_array($consulta);
    }
    
    /**
     * Retorna os dados da consulta em forma de HASH, 
     * 
     * @param resource $consulta
     * @return string[]
     */
    function fetchRow($consulta = NULL){
        if(!$consulta) 
            $consulta = $this->consulta;
        return pg_fetch_row($consulta);
    }
    
    /**
     * Retorna o ultimo ID inserido por uma consulta recente
     * 
     * @return int
     */
    function lastID(){
        return pg_last_oid($this->consulta);
    }
    
    /**
     * Encerra a conexão
     * 
     * @return void
     */
    function close(){
        pg_close($this->conexao);
    }
		
    /**
     * Executa o inicio da transação
     * 
     * @return void
     */
    function beginTrans(){
        $this->execute("BEGIN");	
    }
    
    /**
     * Executa o fim da transação
     * 
     * @return void
     */
    function commitTrans(){
        $this->execute("COMMIT");		
    }
    
    /**
     * Executa o cancelamento da transação
     * 
     * @return void
     */
    function rollBackTrans(){
        $this->execute("ROLLBACK");
    }
    
    /**
     * Returna a lista de databases do servidor
     * 
     * @return string[]
     */
    function databases(){
        $this->execute("SHOW DATABASES");
        $aDatabases = array();
        while ($aReg = $this->fetchRow()){
            $aDatabases[] = $aReg[0];
        }
        return $aDatabases;
    }
    
    /**
     * Lista as colunas da tabela
     * 
     * @param string $tabela
     * @return string[]
     */
    public function carregarColecaoColunasTabela($tabela) {
        
    }
    
    /**
     * Retorna a lista de tabelas do servidor
     * 
     * @return string[]
     */
    public function carregarColecaoTabelas() {
        
    }
    
    /**
     * Retorna os dados das FK da tabela selecionada
     * 
     * @param string $db Banco de dados selecionado
     * @param string $tabela Nome da tabela
     * @param string $coluna Nome da coluna
     * @return string[]
     */
    public function dadosForeignKeyColuna($db, $tabela, $coluna) {
        
    }
}
