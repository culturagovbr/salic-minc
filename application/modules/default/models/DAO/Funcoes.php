<?php
class Funcoes
{
    public static function tratarCEP($value)
    {
        return str_replace('-', '', str_replace('.', '', ($value)));
    }
}
