<?php
/*
 * Created on 19/05/2010
 * Thiago Lênin
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 class AnaliseConteudoConselheiroDAO extends Zend_Db_Table
 {
     protected $_name = 'SAC.dbo.tbAnaliseConteudoConselheiro';
    
    
     public function alterar($dados, $id)
     {
         $obj = new AnaliseConteudoConselheiroDAO();
       
         $db = Zend_Db_Table::getDefaultAdapter();
         $db->setFetchMode(Zend_DB :: FETCH_OBJ);

         return $obj->update($dados, $id);
     }
 }
