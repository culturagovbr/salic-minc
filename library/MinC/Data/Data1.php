<?php
/**
 * Classe para convers�o, valida��o e exibi��o de data e hora
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package library
 * @subpackage library.MinC.Data
 * @copyright � 2010 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Data
{
    /**
     * valida data
     *
     * @access public
     * @static
     * @param string $data
     * @return bool
     */
    public static function validarData($data)
    {
        if (strlen($data) == 10) // data no formato DD/MM/AAAA

        {
            $dia = substr($data, 0, 2);
            $mes = substr($data, 3, 2);
            $ano = substr($data, 6, 4);
        }
        else // data no formato DDMMAAAA

        {
            $dia = substr($data, 0, 2);
            $mes = substr($data, 2, 2);
            $ano = substr($data, 4, 4);
        }

        if (strlen($ano) != 4)
        {
            return false;
        }
        else
        {
            return @checkdate($mes, $dia, $ano); // faz a valida��o da data
        }
    } // fecha m�todo validarData()



    /**
     * valida hora
     *
     * @access public
     * @static
     * @param string $horas
     * @return bool
     */
    public static function validarHora($horas)
    {
        if (strlen($horas) == 8) // hora no formato HH:MM:SS

        {
            $hora    = substr($horas, 0, 2);
            $minuto  = substr($horas, 3, 2);
            $segundo = substr($horas, 6, 2);
        }
        else // hora no formato HHMMSS

        {
            $hora    = substr($horas, 0, 2);
            $minuto  = substr($horas, 2, 2);
            $segundo = substr($horas, 4, 2);
        }

        // retorna falso caso Não tenha sido informados n�meros ou string num�ricas
        if (!is_numeric($hora) || !is_numeric($minuto) || !is_numeric($segundo))
        {
            return false;
        }
        else
        {
            if (($hora < 0 || $hora > 23) || ($minuto < 0 || $minuto > 59) || ($segundo < 0 || $segundo > 59))
            {
                return false;
            }
            else
            {
                return true;
            }
        } // fecha else
    } // fecha m�todo validarHora()



    /**
     * Retorna a data e hora no padr�o americano, exemplo: YYYY-MM-DD HH:MM:SS
     *
     * @access public
     * @static
     * @param string $data_sistema
     * @return string
     */
    public static function dataAmericana($data_sistema)
    {
        $data_sistema    = explode(" ", $data_sistema);
        $data_sistema[0] = explode("/", $data_sistema[0]);
        $data_sistema[0] = array($data_sistema[0][2], $data_sistema[0][1], $data_sistema[0][0]);
        $data_sistema[0] = implode("-", $data_sistema[0]);
        if (!empty($data_sistema[1]))
        {
            return $data_sistema[0] . " " . $data_sistema[1];
        }
        else
        {
            return $data_sistema[0];
        }
    } // fecha m�todo dataAmericana()



    /**
     * Retorna a data e hora no padr�o brasileiro, exemplo: DD/MM/AAAA HH:MM:SS
     *
     * @access public
     * @static
     * @param string $data_sistema
     * @return string
     */
    public static function dataBrasileira($data_sistema)
    {
        $data_sistema    = explode(" ", $data_sistema);
        $data_sistema[0] = explode("-", $data_sistema[0]);
        $data_sistema[0] = array($data_sistema[0][2], $data_sistema[0][1], $data_sistema[0][0]);
        $data_sistema[0] = implode("/", $data_sistema[0]);
        if (!empty($data_sistema[1])) // data e hora

        {
            return $data_sistema[0] . " " . $data_sistema[1];
        }
        else // data

        {
            return $data_sistema[0];
        }
    } // fecha m�todo dataBrasileira()



    /**
     * Retorna a data no formato: Quinta-feira, 18 de outubro de 2007
     *
     * @access public
     * @static
     * @param void
     * @return string
     */
    public static function mostraData()
    {
        $dia    = (int) date("d");
        $mes    = (int) date("m");
        $ano    = date("Y");
        $semana = date("w");

        // m�s por extenso
        $mes_extenso[1] = "janeiro";
        $mes_extenso[2] = "fevereiro";
        $mes_extenso[3] = "mar�o";
        $mes_extenso[4] = "abril";
        $mes_extenso[5] = "maio";
        $mes_extenso[6] = "junho";
        $mes_extenso[7] = "julho";
        $mes_extenso[8] = "agosto";
        $mes_extenso[9] = "setembro";
        $mes_extenso[10] = "outubro";
        $mes_extenso[11] = "novembro";
        $mes_extenso[12] = "dezembro";

        // dia da semana por extenso
        $semana_extenso[0] = "Domingo";
        $semana_extenso[1] = "Segunda-feira";
        $semana_extenso[2] = "Ter�a-feira";
        $semana_extenso[3] = "Quarta-feira";
        $semana_extenso[4] = "Quinta-feira";
        $semana_extenso[5] = "Sexta-feira";
        $semana_extenso[6] = "S�bado";

        if ($dia == 1)
        {
            $dia = "1�";
        }
        return $semana_extenso[$semana] . ", " . $dia . " de " . $mes_extenso[$mes] . " de " . $ano;
    } // fecha m�todo mostraData()



    /**
     * Retorna a sauda��o de acordo com a hora
     *
     * @access public
     * @static
     * @param void
     * @return string
     */
    public static function saudacao()
    {
        // configura a sauda��o
        $hora = date("H");
        if ($hora >= 6 && $hora < 12)
        {
            $saudacao = "Bom dia";
        }
        else if ($hora >= 12 && $hora < 18)
        {
            $saudacao = "Boa tarde";
        }
        else if ($hora >= 18 && $hora <= 23)
        {
            $saudacao = "Boa noite";
        }
        else
        {
            $saudacao = "Boa madrugada";
        }
        return $saudacao;
    } // fecha m�todo saudacao()



    /**
     * Retorna a da data do tipo zend para o tipo que for escolhido
     * @access public
     * @static
     * @param void
     * @return string
     */
    public static function tratarDataZend($data, $tipo, $hora=null)
    {
        if($tipo=="americano")
        {
            if($hora)
            {
                if($data){
                $data = date('Y-m-d H:i:s',strtotime($data));
                }
                else
                {
                     $data = date('Y-m-d H:i:s');
                }
            }
            else
            {
                if($data){
                $data = date('Y-m-d',strtotime($data));
                }
                else
                {
                    $data = date('Y-m-d');
                }

            }
        }
        else
        {
            if($hora)
            {
                if($data){
                $data = date('d/m/Y H:i:s',strtotime($data));
                 }
                else
                {
                    $data = date('d/m/Y H:i:s');
                }
            }else
            {
                if($data){
                $data = date('d/m/Y',strtotime($data));
                 }
                else
                {
                   $data = date('d/m/Y');
                }
            }
        }

        return $data;
    } // fecha m�todo tratarDataZend()



    /**
     * Retorna o status da data
     * - Semaforo Verde    : Data em atraso at� 10 dias
     * - Semaforo Amarelo  : Data em atraso de 10 � 20 dias
     * - Semaforo Vermelho : Data em atraso mais de 20 dias
     * @access public
     * @static
     * @param void
     * @return string
     */
    public static function verificacaoData($datainicial, $datafinal="NOW")
    {
        $datainicial = strtotime($datainicial);
        $datafinal   = strtotime($datafinal);
        $datatirada  = $datainicial - $datafinal;

        $dias = (($datatirada / 3600) / 24);
        //return $dias; die;
        if($dias >= -10 )
        {
            $bl_verde = "public/img/bola_verde.gif";
            return $bl_verde;
        }
        if($dias <= -11 && $dias <= -19)
        {
            $bl_amarelo = "public/img/bola_amarelo.gif";
            return $bl_amarelo;
        }
        if($dias > -20)
        {
            $bl_vermelho = "public/img/bola_vermelho.gif";
            return $bl_vermelho;
        }
    } // fecha m�todo verificacaoData()



	/**
	 * M�todo para fazer compara��o de datas
	 * @access public
	 * @static
	 * @param $datainicial string
	 * @param $datafinal string
	 * @return $dias string
	 */
    public static function CompararDatas($datainicial, $datafinal="NOW")
    {
        $datainicial = strtotime($datainicial);
        $datafinal   = strtotime($datafinal);
        $datatirada  = $datafinal - $datainicial;
        $dias = (($datatirada / 3600) / 24);
        return $dias;
    } // fecha m�todo CompararDatas()

    public static function somarData($data="NOW", $qtdDias=null)
    {
        $data = strtotime($data . "+ ".$qtdDias." day");
        $data = date('d/m/Y', $data);
        return $data;
    }

    public static function DataporExtenso($data){
            $data = date('Y-m-d', strtotime($data));
            $data = explode('-',$data);
            $dia  = $data[2];
            $mes  = $data[1];
            $ano  = $data[0];

            switch($mes) {
              case 01: $mes = "janeiro";
                       break;
              case 02: $mes = "fevereiro";
                       break;
              case 03: $mes = "mar&ccedil;o";
                       break;
              case 04: $mes = "abril";
                       break;
              case 05: $mes = "maio";
                       break;
              case 06: $mes = "junho";
                       break;
              case 07: $mes = "julho";
                       break;
              case 08: $mes = "agosto";
                       break;
              case 09: $mes = "setembro";
                       break;
              case 10: $mes = "outubro";
                       break;
              case 11: $mes = "novembro";
                       break;
              case 12: $mes = "dezembro";
                       break;
            }
            return $dia.' de '.$mes.' de '.$ano;

    }

} // fecha class
