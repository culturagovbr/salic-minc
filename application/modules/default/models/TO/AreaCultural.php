<?php
/*
 * Created on 19/05/2010
 * Thiago Lênin
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
class AreaCultural
{
    private $nomeAreaCultural;
    private $codigoArea;
    
    // $nomeAreaCultural
    public function setNomeAreaCultural($nomeAreaCultural)
    {
        $this->nomeAreaCultural = $nomeAreaCultural;
    }
    
    public function getNomeAreaCultural()
    {
        return $this->nomeAreaCultural;
    }
    
    // $codigoArea
    public function setCodigoArea($codigoArea)
    {
        $this->codigoArea = $codigoArea;
    }
    
    public function getCodigoArea()
    {
        return $this->codigoArea;
    }
}
