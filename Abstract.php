<?php
/**
 * @author Matheus Rosmaninho <matheusmgrosmaninho@gmail.com>
 */
abstract class Profestas_Model_Abstract extends Zend_Db_Table_Abstract
{
    // Definindo os atributos da class
    protected $campos;
    
    /**
     * 
     * Funcao construtora da classe, ela eh responsavel por salvar todos os campos da tabela, 
     * retirando a(s) chave(s) primarias
     * @return void
    */
    public function __construct()
    {
        parent::__construct();
        
        $info = $this->info();
        
        foreach ($info['cols'] as $coluna) {
            if (!$info['metadata'][$coluna]['PRIMARY']) {
                $this->campos[] = $coluna;
            }
        }
    }
    
    /**
     * 
     * Funcao responsavel por realizar consulta no banco
     * @param String $condicao
     * @return array
     */
    public function consultar($condicao = '', $ordem = '', $paginacao = false, $campos = '')
    {
        if ($campos) {
            $select = $this->select()
                           ->from($this, $campos);            
        }
        else {
            $select = $this->select()
                           ->from($this);
        }
        
        if ($condicao) {
            $select = $select->where($condicao);
        }
        
        if ($ordem) {
            $select = $select->order($ordem);
        }
        
        if ($paginacao) {
            return $select;
        }
        else {
            return $this->fetchAll($select)->toArray();
        }
    }
    
    public function consultaEspecifica($id)
    {
        $condicao = $this->getAdapter()->quoteInto($this->_primary[1] . ' = ?', $id);
        
        $retorno = $this->consultar($condicao);
        
        return $retorno[0];
    }

    /**
     *
     * Funcao responsavel pela insercao de um registo no banco
     * @param array $dados 
     * @return boolean 
    */
    public function inserir($dados)
    {
        foreach ($this->campos as $valor) {
            // Verificando se existe a coluna no indice passado pelo usuario
            // se nao tiver eh eliminado, evitando erro na hora do insert
            if ((!empty($dados[$valor]))) {
                $dadosBanco[$valor] = $dados[$valor];
            }
        }

        try {
            $id = $this->insert($dadosBanco);
            return $id;
        } 
        catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * 
     * Funcao responsavel por atualizar um determinado registro no banco
     * @param array $dados
     * @param String $condicao
     * @return boolean
     */
    public function atualizar($dados, $condicao)
    {
        foreach ($this->campos as $valor) {
            // Verificando se existe a coluna no indice passado pelo usuario
            // se nao tiver eh eliminado, evitando erro na hora do insert
            if ((!empty($dados[$valor]))) {
                $dadosBanco[$valor] = $dados[$valor];
            }
        }
        
        try {
            $this->update($dadosBanco, $condicao);
            return true;
        }
        catch (Exception $ex) {
            print_r($ex); die;
            return false;
        }
    }
    
    /**
     * 
     * @param String $condicao
     * @return boolean
     */
    public function deletar($condicao) {
     try {
         $this->delete($condicao);
         return true;
     }
     catch (Exception $ex) {
         print_r($ex); die;
     }
    }
}
