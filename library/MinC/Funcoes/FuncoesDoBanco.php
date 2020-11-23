<?php

class FuncoesDoBanco 
{

	//Fun��o do Banco de Dados fnFormataProcesso
    public static function fnFormataProcesso($processo)
	{
        
		
		if(strlen($processo) == 15)
		{
			if(substr($processo,12,2)> 80)
			{
				$p = substr($processo,0,5) . '.' . substr($processo,5,6) . '/19' . substr($processo,11,2) . '-' . substr($processo,13,2);
			} 
			else
			{
				$p = substr($processo,0,5) . '.' . substr($processo,5,6) . '/20' . substr($processo,11,2) . '-' . substr($processo,13,2);
			}	
		} 
		else
		{
                    
			 $p = substr($processo,0,5) . '.' . substr($processo,5,6) . '/' . substr($processo,11,4) . '-' . substr($processo,15,2);
		}
	       
		return $p;
		  
			
	}
	
	/* Fun��o do banco para retornar o nome do usu�rio 
	 * Usar FuncoesDoBanco::fnNomeUsuario($id);
	 * Retorna String Nome
	 * 
	 */
	public static function fnNomeUsuario($usu_codigo)
	{
		if($usu_codigo == '')
		{
			return '';
		}
		else
		{
			$usuario = new Usuario();
			$dados[] = $usuario->nomeUsuario($usu_codigo);
			
			foreach($dados as $n)
			{
				$nome = $n->usu_nome; 
			}
			
			return $nome;
		}
		
		
	}

	
	/* Fun��o do banco fnOutrasFontes 
	 * Usar FuncoesDoBanco::fnOutrasFontes($idPronac);
	 * Retorna Float valor
	 * 
	 */
	public static function fnOutrasFontes($idPronac)
	{
		$valor = 0;
		
		if($idPronac == '')
		{
			return '';
		}
		else
		{
			$pp = new PlanilhaProjeto();
			$dados[] = $pp->outrasFontes($idPronac);
			
			foreach($dados as $n)
			{
				if($n[0]->valor <> 0)
				{
					$valor = $n[0]->valor;
				}		
			}
			
		}
		
		return (float)$valor;
	}
	
}

