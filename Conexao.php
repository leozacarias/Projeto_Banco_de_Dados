<?php



include_once("Config.php");



class Conexao {

    private $tabela;

    static $conexao;

    private $clausula;

    function __construct() {

        try {

            $pdo = new PDO("mysql:host=".HOST.";dbname=".DBNAME, USER, PASSWORD);

        } catch (PDOException $e) {

            echo $e->getMessage();

        }

        //Instancia da conexao, atribuindo todos os metodos

        self::$conexao = $pdo;

    }

    //Setando a tabela a ser utilizada

    function setTabela($tabela){

    	$this->tabela = $tabela;

    }

    //Parametro a ser adicionado antes de qualquer operação

    function where($parametro, $valor) {

        if ($parametro != NULL && $valor != NULL) {

            $this->clausula = "WHERE {$parametro} = '{$valor}'";

        } else {

            $this->clausula = NULL;

        }

        return $this->clausula;

    }

    /*

    *

    *ALGUNS EXEMPLOS DE UTILIZAÇÃO

    *

    */



    function getAll(){

        $select = self::$conexao->prepare("SELECT * FROM {$this->tabela}");

        $select->execute();



        if ($select->rowCount()) {

            return $select->fetchAll(PDO::FETCH_ASSOC);

        }

    }



    //Retorna uma coluna "Clausula é obrigatoria"

    function getAllBy() {

        if ($this->clausula != NULL || $this->clausula != "") {

            $select = self::$conexao->prepare("SELECT * FROM {$this->tabela} {$this->clausula}");

            $select->execute();

            if ($select->rowCount()) {

                return $select->fetch(PDO::FETCH_ASSOC);

            } else {

                return false;

            }

        } else {

            return false;

        }

    }



    //Inserir dados no banco

    function insert($dados) {

        $chaves = implode(',', array_keys($dados));

        $valores = "'" . implode("','", $dados) . "'";



        //Criando Segurança para cadastro "Verificando a existencia de um valor do array ser vazio"

        $count = 0;

        foreach ($dados as $key => $valor) {

            if ($valor == null) {

                $count++;

                break;

            }

        }



        if ($count == 0) {

            $insert = self::$conexao->prepare("INSERT INTO {$this->tabela} ({$chaves}) VALUES({$valores})");

            $insert->execute();

            if ($insert->rowCount()) {

                return true;

            }

        }

    }



    //Alterar dados no banco



    /*----------------------------------------------------------------------------------------------

    *

    *

    *   PRECISA SER UM ARRAY ASSOCIATIVO, NOMEANDO AS CHAVES COM O NOME DAS COLUNAS A SER MODIFICADA

    *

    *

    *   $dados = array('tb_email'=>'novoemail@email.com','tb_senha'=>'novaSenha123');

    *

    *

    *

    *----------------------------------------------------------------------------------------------

    */

    function update(array $dados) {



        //Criando Segurança para cadastro "Verificando a existencia de um valor do array ser vazio"

        $count = 0;

        foreach ($dados as $key => $valor) {

            if ($valor == null) {

                continue;

            }

            $update = self::$conexao->prepare("UPDATE {$this->tabela} SET {$key} = '{$valor}' {$this->clausula}");

            $update->execute();

            if ($update->rowCount()) {

                $count++;

            }

        }

        if ($count > 0) {

            return true;

        }

    }



    function delete() {

        if($this->clausula != NULL){

            $prepare = self::$conexao->prepare("DELETE FROM {$this->tabela} {$this->clausula}");

            $prepare->execute();


            if ($prepare->rowCount()) {

                return true;

            }

        }

    }

}
