<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paTransformarPropostaEmProjeto
 */
class paTransformarPropostaEmProjeto extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'paTransformarPropostaEmProjeto';

    #public function execSP($idProposta, $CNPJCPF, $idOrgao, $idUsuario){
    /**
     * @author Alysson Vicu�a de Oliveira
     * Chamada da Procedure respons�vel por Transformar uma Proposta em Projeto dentro do Salic
     * @param $idProposta
     * @param $CNPJCPF
     * @param $idOrgao
     * @param $idUsuario
     * @param $nrProcesso
     * @return string|Zend_Db_Statement_Interface
     */
    #public function execSP($idProposta, $CNPJCPF, $idOrgao, $idUsuario){
    public function execSP($idProposta, $CNPJCPF, $idOrgao, $idUsuario, $nrProcesso)
    {
        try {
            $rodar = "exec " . $this->_banco .".". $this->_name . ' ' . $idProposta .',"'. $CNPJCPF.'",'. $idOrgao.','. $idUsuario.',"'. $nrProcesso . '"';
            
            return  $this->getAdapter()->query($rodar);
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }
}
