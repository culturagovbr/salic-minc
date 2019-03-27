<?php

class Zend_View_Helper_ContadorTextarea
{
    /**
     * Método com os parametros com contador
     * @access public
     * @param string $campo (campo textarea)
     * @param string $contador (campo que exibe a quantidade de caracteres restantes)
     * @param integer $limite (quantidade máxima de caracteres)
     * @return string $eventos
     */
    public function contadorTextarea($campo, $contador, $limite)
    {
        $eventos = "onkeydown=\"caracteresTextarea(this." . $campo . ", this." . $contador . ", " . $limite . ");\" 
					onkeyup=\"caracteresTextarea(this." . $campo . ", this." . $contador . ", " . $limite . ");\"";
        return $eventos;
    }
}
