<?php
/*
 * Created on 19/05/2010
 * Thiago Lênin
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class SeguimentoCultural
 {
     private $nomeSeguimentoCultural;
     private $codigo;
     private $idOrgao;
     private $estado;
    
     // $nomeSeguimentoCultural;
     public function setNomeSeguimentoCultural($nomeSeguimentoCultural)
     {
         $this->nomeSeguimentoCultural = $nomeSeguimentoCultural;
     }
    
    
     public function getNomeSeguimentoCultural()
     {
         return $this->nomeSeguimentoCultural;
     }
    
     // $codigo;
     public function setCodigo($codigo)
     {
         $this->codigo = $codigo;
     }
    
     public function getCodigo()
     {
         return $this->codigo;
     }
    
     // $idOrgao;
     public function setIdOrgao($idOrgao)
     {
         $this->idOrgao = $idOrgao;
     }
    
     public function getIdOrgao()
     {
         return $this->idOrgao;
     }
    
     // $estado;
     public function setEstado($estado)
     {
         $this->estado = $estado;
     }
    
     public function getEstado()
     {
         return $this->estado;
     }
 }
