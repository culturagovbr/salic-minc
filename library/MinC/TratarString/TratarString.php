<?php

class TratarString
{
    public static function escapeString($string)
    {
        $string = str_replace("'", "''", $string);
        return $string;
    }

    public static function criarSlug($string)
    {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        return $slug;
    }

    public static function tratarTextoRico($string)
    {
        $string = trim($string);
        $string = strip_tags($string);
        $string = str_replace(array("\r\n", "\r", "\n"), "<br>", $string);
        $string = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $string);

        return $string;
    }

    public static function converterParaUTF8($string)
    {
        if (mb_detect_encoding($string, 'UTF-8', true) === false) {
            $string = utf8_encode($string);
        }

        return $string;
    }

    public static function tratarTextoRicoParaISO($string)
    {
        return utf8_decode(trim(strip_tags($string)));
    }

    public static function tratarTextoRicoComCaracteresDoWord($string)
    {
        $replace = array(
            "‘" => "'",
            "’" => "'",
            "”" => '"',
            "“" => '"',
            "–" => "-",
            "—" => "-",
            "…" => "..."
        );

        foreach ($replace as $caractereWord => $caractereUtf8) {
            $stringTratada = str_replace($caractereWord, $caractereUtf8, $string);
        }
        return $stringTratada;
    }
}
