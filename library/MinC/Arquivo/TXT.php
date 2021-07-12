<?php
/**
 * Classe para trabalhar com arquivos TXT
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package library
 * @subpackage library.MinC.Arquivo
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class TXT
{
	/**
	 * M�todo para gravar em um arquivo txt
	 * @access public
	 * @static
	 * @param string $msg
	 * @param string $arq
	 * @return void
	 */
	public static function gravarTXT($msg, $arq)
	{
		try
		{
			$ponteiro = fopen($arq, "a"); // abre o arquivo para escrita
			if (!$ponteiro)
			{
				throw new Exception("Erro ao tentar abrir o arquivo <strong>$arq</strong>!");
			}

			// CONTEÚDO do arquivo
			$conteudo = $msg;

			if (!fwrite($ponteiro, $conteudo)) // efetua a grava��o
			{
				throw new Exception("Erro ao tentar gravar no arquivo <strong>$arq</strong>!");
			}
			fclose($ponteiro); // fecha o arquivo
		} // fecha try
		catch (Exception $e)
		{
			echo $e->getMessage();
			exit();
		}
	} // fecha m�todo gravarTXT()



	/**
	 * M�todo para ler um arquivo txt
	 * @access public
	 * @static
	 * @param string $arq
	 * @return void
	 */
	public static function lerTXT($arq)
	{
		try
		{
			$ponteiro = fopen($arq, "r"); // abre o arquivo para leitura
			if (!$ponteiro)
			{
				throw new Exception("Erro ao tentar abrir o arquivo <strong>$arquivo</strong>!");
			}

			while (!feof($ponteiro)) // l� o arquivo, linha por linha at� chegar no final
			{
				$linha = fgets($ponteiro, 4096);
				echo $linha . "<br /><br />";
			} // fecha while
			fclose($ponteiro); // fecha o arquivo
		} // fecha try
		catch (Exception $e)
		{
			echo $e->getMessage();
			exit();
		}
	} // fecha m�todo lerTXT()



	/**
	 * Contador de visitas
	 * @access public
	 * @static
	 * @param string $txt
	 * @return string
	 */
	public static function contadorVisitas($txt)
	{
		try
		{
			// obtendo o n�mero de visitas
			$arquivo = fopen($txt, "r");
			if (!$arquivo)
			{
				throw new Exception("Erro ao tentar abrir o arquivo <strong>$txt</strong>!");
			}
			$visitas = fgets($arquivo, 1024);
			fclose($arquivo);

			if ($_SESSION["contador"] != $_SERVER["REMOTE_ADDR"])
			{
				// atualizando n�mero de visitas
				$arquivo = fopen($txt, "r+");
				$visitas += 1;
				if (!fwrite($arquivo, $visitas))
				{
					throw new Exception("Erro no contador!");
				}
				fclose($arquivo);
				$_SESSION["contador"] = $_SERVER["REMOTE_ADDR"];
			} // fecha if

			return number_format($visitas, 0, '', '.');
		} // fecha try
		catch (Exception $e)
		{
			echo $e->getMessage();
			exit();
		}
	} // fecha m�todo contadorVisitas()



	/**
	 * Visitantes Online
	 * @access public
	 * @static
	 * @param string $filename
	 * @return string
	 */
	public static function visitantesOnline($filename)
	{
		$explain   = "";
		$additions = 0;
		$timer     = 10;

		if (!$datei)
		{
			$datei = dirname(__FILE__) . "/$filename";
		}
		$time = @time();
		$space = " ";
		$ip = $REMOTE_ADDR;
		$string = "$ip|$time\n";
		$a = fopen("$filename", "a+");
		fputs($a, $string);
		fclose($a);

		$timeout = time() - (60 * $timer);
		$all = "";
		$i = 0;
		$datei = file($filename);
		for ($num = 0; $num < count($datei); $num++)
		{
			$pieces = explode("|", $datei[$num]);
			if ($pieces[1] > $timeout)
			{
				$all.= $pieces[0];
				$all.= ",";
			}
			$i++;
		} // fecha for

		$all = substr($all, 0, strlen($all) - 1);
		$arraypieces = explode(",", $all);
		$useronline = count(array_flip(array_flip($arraypieces)));

		echo $explain;
		echo $space;

		// delete
		$dell = "";
		for ($numm = 0; $numm < count($datei); $numm++)
		{
			$tiles = explode("|", $datei[$numm]);
			if ($tiles[1] > $timeout)
			{
				$dell.= "$tiles[0]|$tiles[1]";
			}
		} // fecha for

		if (!$datei)
		{
			$datei = dirname(__FILE__) . "/$filename";
		}
		$time = @time();
		$ip = $REMOTE_ADDR;
		$string = "$dell";
		$a = fopen("$filename", "w+");
		fputs($a, $string);
		fclose($a);

		return $useronline + $additions;
	} // fecha m�todo visitantesOnline()

} // fecha class
