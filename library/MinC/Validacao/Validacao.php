<?php
/**
 * Classe para realizar valida��es de campos especiais
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package library
 * @subpackage library.MinC.Validacao
 * @copyright � 2010 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Validacao
{
	/**
	 * M�todo para adicionar m�scara de CPF ou CNPJ conforme o caso
	 *
	 * @access public
	 * @static
	 * @param string $valor
	 * @return string
	 */
	public static function mascaraCPFCNPJ($valor)
	{
		$valor = trim($valor);
		$count = strlen($valor);
		if ($count == 11)
		{
			return (Mascara::addMaskCPF($valor));
		}
		else
		{
			return (Mascara::addMaskCNPJ($valor));
		}
	} // fecha m�todo mascaraCPFCNPJ()



	/**
	 * M�todo para validar fone
	 *
	 * @access public
	 * @static
	 * @param string $fone
	 * @return bool
	 */
	public static function validarFone($fone)
	{
		if (strlen($fone) != 8 && strlen($fone) != 10)
		{
			return false;
		}
		else
		{
			return is_numeric($fone);
		}
	} // fecha m�todo validarFone()



	/**
	 * valida email
	 *
	 * @access public
	 * @static
	 * @param string $email
	 * @return bool
	 */
	public static function validarEmail($email)
	{
		if (strlen($email) < 8 || substr_count($email, "@") != 1 || substr_count($email, ".") == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	} // fecha m�todo validarEmail()



	/**
	 * valida endere�o eletr�nico
	 *
	 * @access public
	 * @static
	 * @param string $url
	 * @return bool
	 */
	public static function validarURL($url)
	{
		return preg_match('|^http(s)?://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	} // fecha m�todo validarURL()



	/**
	 * valida CEP
	 *
	 * @access public
	 * @static
	 * @param string $cep
	 * @return bool
	 */
	public static function validarCEP($cep)
	{
		if (strlen($cep) != 8)
		{
			return false;
		}
		else
		{
			return is_numeric($cep);
		}
	} // fecha m�todo validarCEP()

	/**
	 * valida Data
	 *
	 * @access public
	 * @static
	 * @param string $data
	 * @return bool
	 */

	public static function validarData($dat){

            $data = explode("/","$dat"); // fatia a string $dat em pedados, usando / como refer�ncia
            $d = $data[0];
            $m = $data[1];
            $y = $data[2];

            // verifica se a data � v�lida!
            $res = checkdate($m,$d,$y);
            if ($res == 0){
                return false;
            }

            return true;

	} // fecha m�todo validarData()



	/**
	 * valida CPF
	 *
	 * @access public
	 * @static
	 * @param string $cpf
	 * @return bool
	 */
	public static function validarCPF($cpf)
	{
		if (!is_numeric($cpf) || strlen($cpf) != 11)
		{
			return false;
		}
		else if ($cpf == "11111111111" || $cpf == "22222222222" ||
		$cpf == "33333333333" || $cpf == "44444444444" ||
		$cpf == "55555555555" || $cpf == "66666666666" ||
		$cpf == "77777777777" || $cpf == "88888888888" ||
		$cpf == "99999999999" || $cpf == "00000000000" ||
		$cpf == "12345678909")
		{
			return false;
		}
		else
		{
			// verifica o primeiro d�gito verificador
			$total = 0;
			$casas = array(10, 9, 8, 7, 6, 5, 4, 3, 2);
			for ($i = 0; $i < 9; $i++)
			{
				$total += (substr($cpf, $i, 1) * $casas[$i]);
			}
			$total %= 11; // pega o resto da divis�o por 11
			if ($total < 2)
			{
				$d1 = 0;
			}
			else
			{
				$d1 = 11 - $total;
			}

			// verifica o segundo d�gito verificador
			$total = 0;
			$casas = array(11, 10, 9, 8, 7, 6, 5, 4, 3, 2);
			for ($i = 0; $i < 10; $i++)
			{
				$total += (substr($cpf, $i, 1) * $casas[$i]);
			}
			$total %= 11; // pega o resto da divis�o por 11
			if ($total < 2)
			{
				$d2 = 0;
			}
			else
			{
				$d2 = 11 - $total;
			}

			$dv = substr($cpf, 9, 2); // d�gito verificador
			$d = $d1 . $d2; // d�gito verificador calculado
			if ($dv == $d)
			{
				return true;
			}
			else
			{
				return false;
			}
		} // fecha else
	} // fecha m�todo validarCPF()



	/**
	 * valida CNPJ
	 *
	 * @access public
	 * @static
	 * @param string $cnpj
	 * @return bool
	 */
	public static function validarCNPJ($cnpj)
	{
		if (!is_numeric($cnpj) || strlen($cnpj) != 14)
		{
			return false;
		}
		else if ($cnpj == "11111111111111" || $cnpj == "22222222222222" ||
		$cnpj == "33333333333333" || $cnpj == "44444444444444" ||
		$cnpj == "55555555555555" || $cnpj == "66666666666666" ||
		$cnpj == "77777777777777" || $cnpj == "88888888888888" ||
		$cnpj == "99999999999999" || $cnpj == "00000000000000")
		{
			return false;
		}
		else
		{
			// verifica o primeiro d�gito verificador
			$total = 0;
			$casas = array(5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
			for ($i = 0; $i < 12; $i++)
			{
				$total += (substr($cnpj, $i, 1) * $casas[$i]);
			}
			$total %= 11; // pega o resto da divis�o por 11
			if ($total < 2)
			{
				$d1 = 0;
			}
			else
			{
				$d1 = 11 - $total;
			}

			// verifica o segundo d�gito verificador
			$total = 0;
			$casas = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
			for ($i = 0; $i < 13; $i++)
			{
				$total += (substr($cnpj, $i, 1) * $casas[$i]);
			}
			$total %= 11; // pega o resto da divis�o por 11
			if ($total < 2)
			{
				$d2 = 0;
			}
			else
			{
				$d2 = 11 - $total;
			}

			$dv = substr($cnpj, 12, 2); // d�gito verificador
			$d = $d1 . $d2; // d�gito verificador calculado
			if ($dv == $d)
			{
				return true;
			}
			else
			{
				return false;
			}
		} // fecha else
	} // fecha m�todo validarCNPJ()



	/**
	 * valida PRONAC
	 *
	 * @access public
	 * @static
	 * @param string $pronac
	 * @return bool
	 */
	public static function validarPRONAC($pronac)
	{
		$pronac = trim($pronac);

		if (!is_numeric($pronac) || strlen($pronac) > 7)
		{
			return false;
		}
		else
		{
			return false;
		}
	} // fecha m�todo validarPRONAC()



	public static function validarNrProcesso($nrprocesso)

	{
		if (strlen($nrprocesso) != 17)
		{
			return false;
		}
		$corpo_proc = substr($nrprocesso, 0, -2);
		$dig_proc   = substr($nrprocesso, -2);
		$orgao      = substr($nrprocesso, 0, 5);
		$ano        = substr($nrprocesso, 11, 4);

		$x    = 0;
		$y    = 16;
		$soma = 0;
		for ($x = 0 ; $x < 15 ; $x++)
		{
			$soma += substr($corpo_proc, $x , 1) * $y;
			$y--;
		}
		$resto = $soma % 11;
		$dig1  = 11 - $resto;
		if (strlen($dig1) == 2)
		{
			$dig1 = substr($dig1, 1, 1);
		}
		$parte2 = $corpo_proc . $dig1;

		$x    = 0;
		$y    = 17;
		$soma = 0;
		for ($x = 0 ; $x < 16 ; $x++)
		{
			$soma += substr($parte2, $x , 1) * $y;
			$y--;
		}
		$resto = $soma % 11;
    $dig2  = 11 - $resto;
    if (strlen($dig2) == 2)
    {
        $dig2 = substr($dig2, 1, 1);
    }
    $parte2 .= $dig2;
    if ($parte2 == $nrprocesso)
    {
        return true;
    }
    else
    {
        return false;
    }
 }



} // fecha class
