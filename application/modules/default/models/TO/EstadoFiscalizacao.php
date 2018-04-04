<?php
/*
 * Created on 20/05/2010
 * Thiago Lênin
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class EstadoFiscalizacao
 {
     private $descricaoEstado;
    
     // $descricaoEstado
     public function setDescricaoEstado($descricaoEstado)
     {
         $this->descricaoEstado = $descricaoEstado;
     }
    
     public function getDescricaoEstado()
     {
         return $this->descricaoEstado;
     }
 }
