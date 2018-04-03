<?php
/**
 * Classe para retirada de mascaras javascript e insercoo em campos vindos do banco
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package library
 * @subpackage library.MinC.Validacao
 * @copyright @ 2010 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Mascara
{
	/**
	 * deleta do telefone
	 *
	 * @access public
	 * @static
	 * @param string $fone
	 * @return string
	 */
	public static function delMaskFone($fone)
	{
		$fone = str_replace("(", "", $fone);
		$fone = str_replace(")", "", $fone);
		$fone = str_replace(" ", "", $fone);
		$fone = str_replace("-", "", $fone);
		$fone = str_replace("/", "", $fone);
		$fone = str_replace(".", "", $fone);

		return $fone;
	} // fecha m�todo delMaskFone()



	/**
	 * deleta do email
	 *
	 * @access public
	 * @static
	 * @param string $email
	 * @return string
	 */
	public static function delMaskEmail($email)
	{
		// elimina os erros mais comuns de digita��o de e-mails
		$email = str_replace(" ", "", $email);
		$email = str_replace("/", "", $email);
		$email = str_replace("@.", "@", $email);
		$email = str_replace(".@", "@", $email);
		$email = str_replace(",", ".", $email);
		$email = str_replace(";", ".", $email);

		return $email;
	} // fecha m�todo delMaskEmail()



	/**
	 * deleta do cep
	 *
	 * @access public
	 * @static
	 * @param string $cep
	 * @return string
	 */
	public static function delMaskCEP($cep)
	{
		$cep = str_replace(" ", "", $cep);
		$cep = str_replace(".", "", $cep);
		$cep = str_replace("-", "", $cep);
		$cep = str_replace(",", "", $cep);
		$cep = str_replace(";", "", $cep);
		$cep = str_replace("/", "", $cep);

		return $cep;
	} // fecha m�todo delMaskCEP()



	/**
	 * deleta do cpf
	 *
	 * @access public
	 * @static
	 * @param string $cpf
	 * @return string
	 */
	public static function delMaskCPF($cpf)
	{
		$cpf = str_replace(" ", "", $cpf);
		$cpf = str_replace(".", "", $cpf);
		$cpf = str_replace("-", "", $cpf);
		$cpf = str_replace(",", "", $cpf);
		$cpf = str_replace(";", "", $cpf);
		$cpf = str_replace("/", "", $cpf);

		return $cpf;
	} // fecha m�todo delMaskCPF()



	/**
	 * deleta do cnpj
	 *
	 * @access public
	 * @static
	 * @param string $cnpj
	 * @return bool
	 */
	public static function delMaskCNPJ($cnpj)
	{
		$cnpj = str_replace(" ", "", $cnpj);
		$cnpj = str_replace(".", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(",", "", $cnpj);
		$cnpj = str_replace(";", "", $cnpj);
		$cnpj = str_replace("/", "", $cnpj);

		return $cnpj;
	} // fecha m�todo delMaskCNPJ()



	/**
	 * deleta do cpf e cnpj
	 *
	 * @access public
	 * @static
	 * @param string $cpfcnpj
	 * @return bool
	 */
	public static function delMaskCPFCNPJ($cpfcnpj)
	{
		$cpfcnpj = self::delMaskCPF($cpfcnpj);
		$cpfcnpj = self::delMaskCNPJ($cpfcnpj);

		return $cpfcnpj;
	} // fecha m�todo delMaskCPFCNPJ()



	/**
	 * deleta da moeda e adiciona
	 *
	 * @access public
	 * @static
	 * @param string $valor
	 * @return bool
	 */
	public static function delMaskMoeda($valor)
	{
		$valor = str_replace(" ", "", $valor);
		$valor = str_replace(".", "", $valor);
		$valor = str_replace(",", ".", $valor);

		return $valor;
	}

	/**
	 * @access public
	 * @static
	 * @param string $valor
	 * @return string
     *
     * @author Ruy Ferreira <ruyjfs@gmail.com>
     * @since 04/12/2017
	 */
	public static function addMaskMoeda($valor)
	{
		return number_format($valor, 2, ',', '.');
	}



	/**
	 * adiciona no telefone
	 *
	 * @access public
	 * @static
	 * @param string $fone
	 * @return string
	 */
	public static function addMaskFone($fone)
	{
		$s1 = substr($fone, 0, 4);
		$s2 = substr($fone, 4, 4);

		return $s1 . "-" . $s2;
	} // fecha m�todo addMaskFone()



	/**
	 * adiciona no cep
	 * @access public
	 * @static
	 * @param string $cep
	 * @return string
     * @author Ruy Junior Ferreira Silva
     * @since 01/05/2016
	 */
	public static function addMaskCEP($cep)
	{
	    if (empty($cep)) {
	        return '';
        } else {
            $s1 = substr($cep, 0, 2);
            $s2 = substr($cep, 2, 3);
            $s3 = substr($cep, 5, 3);

            return $s1 . "." . $s2 . "-" . $s3;
        }
	}

	/**
	 * Formata texto para CPF ou CNPJ conforme a quantidade de n�meros.
	 *
	 * @access public
	 * @static
	 * @param string $texto
	 * @return string
	 */
	public static function addMaskCpfCnpj($texto){
        if(strlen(trim($texto)) > 11) {
            $cpfCnpj = Mascara::addMaskCNPJ($texto);
        } else {
            $cpfCnpj = Mascara::addMaskCPF($texto);
        }

        return $cpfCnpj;
	}

	/**
	 * adiciona no cpf
	 *
	 * @access public
	 * @static
	 * @param string $cpf
	 * @return string
	 */
	public static function addMaskCPF($cpf)
	{ 
		$s1 = substr($cpf, 0, 3);
		$s2 = substr($cpf, 3, 3);
		$s3 = substr($cpf, 6, 3);
		$s4 = substr($cpf, 9, 2);

		return $s1 . "." . $s2 . "." . $s3 . "-" . $s4;
	} // fecha m�todo addMaskCPF()


        public static function addMaskProcesso($processo)
	{
		$s1 = substr($processo, 0, 5);
		$s2 = substr($processo, 5, 6);
		$s3 = substr($processo, 11, 4);
		$s4 = substr($processo, 15, 2);

		return $s1 . "." . $s2 . "/" . $s3 . "-" . $s4;
	} // fecha m�todo addMaskCPF()

        
        public static function delMaskProcesso($processo)
	{
            // 01400.123456/2014-77
            $processo = str_replace(" ", "", $processo);
            $processo = str_replace(".", "", $processo);
            $processo = str_replace("/", "", $processo);
            $processo = str_replace("-", "", $processo);

            return $processo;
                
	} // fecha m�todo delMaskProcesso()



	/**
	 * adiciona no cnpj
	 *
	 * @access public
	 * @static
	 * @param string $cnpj
	 * @return bool
	 */
	public static function addMaskCNPJ($cnpj)
	{
		$s1 = substr($cnpj, 0, 2);
		$s2 = substr($cnpj, 2, 3);
		$s3 = substr($cnpj, 5, 3);
		$s4 = substr($cnpj, 8, 4);
		$s5 = substr($cnpj, 12, 2);

		return $s1 . "." . $s2 . "." . $s3 . "/" . $s4 . "-" . $s5;
	} // fecha m�todo addMaskCNPJ()



	/**
	 * adiciona na data brasileira
	 *
	 * @access public
	 * @static
	 * @param string $data
	 * @return bool
	 */
	public static function addMaskDataBrasileira($data)
	{
		$s1 = substr($data, 0, 2);
		$s2 = substr($data, 2, 2);
		$s3 = substr($data, 4, 4);
		return $s1 . "/" . $s2 . "/" . $s3;
	}

	/**
	 * @access public
	 * @static
	 * @param string $strDate
	 * @return string
     *
     * @author Ruy Ferreira <ruyjfs@gmail.com>
     * @since 04/12/2017
	 */
	public static function convertDateToView($strDate)
	{
        return reset(explode(' ', new Zend_Date($strDate)));
	}
}