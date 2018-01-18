<?php
class Gerarsenha extends MinC_Controller_Action_Abstract
{
    public static function gerasenha($numcarats=8, $lmin=true, $lmai=true, $numeros=true, $simb=false)
    {
        $lemin="abcdefghijklmnopqrstuvwxyz";
        $lemai="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $nmeros="0123456789";
        $simbolos="^~`&()";
        $senha="";
        $caracteres="";
        if ($lmin) {
            $caracteres.=$lemin;
        }
        if ($lmai) {
            $caracteres.=$lemai;
        }
        if ($numeros) {
            $caracteres.=$nmeros;
        }
        if ($simb) {
            $caracteres.=$simbolos;
        }
        $length=strlen($caracteres);
        for ($i=1; $i<=$numcarats; $i++) {
            $rand=mt_rand(1, $length);
            $senha.=$caracteres[$rand-1];
        }
        return $senha;
    }
}
