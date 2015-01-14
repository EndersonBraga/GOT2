<?php
/**
 * Classe de Conexão com SQLServer utilizando biblioteca nativa php_sqlsrv_55_ts.dll da Microsoft
 * 
 * @author Luiz Leão <luizleao@gmail.com>
 */
class ConexaoSqlServer implements IConexao{
    public $conexao;
    public $consulta;  
    public $msg;
    public $db;
    public $last_id;
    public $data_cadastro_padrao = "now()";

    function __construct($servidor = 'Local'){
        switch ($servidor){
            case 'Local':
                $this->set_conexao(HOST,USER,PASSWD,DB);
            break;

            case 'Vazia':
            break;

            default:
                die("Servidor $servidor inexistente");
            break;
         }
    }
    
    /**
     * 
     * @param type $host
     * @param type $user
     * @param type $senha
     * @param type $bd
     */
    function set_conexao($host,$user,$senha,$bd=NULL){
        try{
            //$connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database"=>"AdventureWorks");
            $aConnectInfo = array();
            $aConnectInfo['UID'] = $user;
            $aConnectInfo['PWD'] = $senha;
            if($bd != NULL){
                $aConnectInfo['Database'] = $bd;
            }
            $this->conexao = sqlsrv_connect($host,$aConnectInfo);// or die(sqlsrv_error());
        } catch(Exception $e){
            $this->msg = $e->getMessage();
        }
    }

    /**
     * 
     * @param type $sql
     * @return boolean
     */
    function execute($sql){
        $consulta = sqlsrv_query($this->conexao, $sql);
        if($consulta){
            $this->consulta = $consulta;
            return true;
        }
        else{
            //print $sql;
            if(($aErrors = sqlsrv_errors()) != null){
                foreach($aErrors as $error){
                    $msg[] = $error['SQLSTATE']." - ".$error['code']." - ".$error['message'];
                }
                $this->msg = implode(", ", $msg);
                return false;
            }
        }
    }

    /**
     * 
     * @param type $consulta
     * @return type
     */
    function numRows($consulta = NULL){
        if(!$consulta) {
            $consulta = $this->consulta;
        }
        return (int) sqlsrv_num_rows($consulta);
    }

    /**
     * 
     * @param type $consulta
     * @return type
     */
    function fetchReg($consulta = NULL){
        if(!$consulta) {
            $consulta = $this->consulta;
        }
        return sqlsrv_fetch_array($consulta);
    }

    /**
     * 
     * @param type $consulta
     * @return type
     */
    function fetchRow($consulta = NULL){
        if(!$consulta) {
            $consulta = $this->consulta;
        }
        return sqlsrv_fetch_row($consulta);
    }

    /**
     * 
     * @return type
     */
    function lastID(){
        return sqlsrv_insert_id($this->conexao);
    }

    /**
     * 
     */
    function close(){
        sqlsrv_close($this->conexao);
    }

    /**
     * 
     */
    function beginTrans(){
        $this->execute("BEGIN");	
    }

    /**
     * 
     */
    function commitTrans(){
        $this->execute("COMMIT");		
    }

    /**
     * 
     */
    function rollBackTrans(){
        $this->execute("ROLLBACK");
    }

    /**
     * 
     * @return type
     */
    function databases(){
        $this->execute("select name from sys.databases");
        $aDatabases = array();
        while ($aReg = $this->fetchReg()){
            $aDatabases[] = $aReg[0];
        }
        return $aDatabases;
    }

    /**
     * 
     * @param type $tabela
     * @return type
     */
    public function carregarColecaoColunasTabela($tabela) {
        $sql = "select
                    T1.COLUMN_NAME as Field,
                    T1.DATA_TYPE as 'Type', 
                    T1.IS_NULLABLE as 'Null',
                    case
                        when SUBSTRING(T2.CONSTRAINT_NAME,0, 3) = 'PK' then 'PRI'
                        when SUBSTRING(T2.CONSTRAINT_NAME,0, 3) = 'FK' then 'MUL'
                        else '' 
                    End as 'Key',
                    T1.COLUMN_DEFAULT as 'Default', 
                    case
                        when SUBSTRING(T2.CONSTRAINT_NAME,0, 3) = 'PK' then 'auto_increment'
                        else '' 
                    End as Extra
                from 
                    INFORMATION_SCHEMA.COLUMNS T1
                left join information_schema.KEY_COLUMN_USAGE T2
                        on (T1.TABLE_NAME = T2.TABLE_NAME
                            and T1.COLUMN_NAME = T2.COLUMN_NAME
                            and SUBSTRING(T2.CONSTRAINT_NAME,0, 3) <> 'IX')
                where 
                    T1.TABLE_NAME='$tabela'";
        $this->execute($sql);
        
        $aDados = array();
        while ($aReg = $this->fetchReg()){
            $aDados[] = $aReg;
        }
        return $aDados;
    }

    /**
     * 
     * @param type $db
     * @param type $tabela
     * @param type $coluna
     * @return type
     */
    public function dadosForeignKeyColuna($db, $tabela, $coluna) {
        $this->execute("select 
                            /*r.CONSTRAINT_NAME,
                            t1.TABLE_NAME as tabela_de, 
                            k1.COLUMN_NAME as campo_de,*/
                            t2.TABLE_NAME as tabela_para,
                            k2.COLUMN_NAME as campo_para
                        from 
                            INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS r
                        join INFORMATION_SCHEMA.TABLE_CONSTRAINTS t1 on (t1.CONSTRAINT_NAME = r.CONSTRAINT_NAME)
                        join INFORMATION_SCHEMA.TABLE_CONSTRAINTS t2 on (t2.CONSTRAINT_NAME = r.UNIQUE_CONSTRAINT_NAME)
                        join INFORMATION_SCHEMA.KEY_COLUMN_USAGE k1 on (k1.CONSTRAINT_NAME = r.CONSTRAINT_NAME)
                        join INFORMATION_SCHEMA.KEY_COLUMN_USAGE k2 on (k2.CONSTRAINT_NAME = r.UNIQUE_CONSTRAINT_NAME)
                        where 
                            t1.table_name            = '$tabela'
                            and k1.COLUMN_NAME       = '$coluna'
                            and r.CONSTRAINT_CATALOG = '$db'");

        return $this->fetchReg();
    }

    /**
     * 
     * @return type
     */
    public function carregarColecaoTabelas() {
        $this->execute("select table_name, table_schema from INFORMATION_SCHEMA.TABLES");
        $aDados = array();
        while ($aReg = $this->fetchReg()){
            $aDados[] = $aReg;
        }
        return $aDados;
    }
}