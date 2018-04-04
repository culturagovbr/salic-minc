<?php

class TratarArray {
    public static function multi_array_search($variavelprocurada, $Array) {
        $Key = '';
        $co = 0;
        foreach ($Array as $key => $value) {
            if (is_array($value)) {
                $value = array_search($variavelprocurada, $value);
                
                if (!empty($value)) {//needle is found
                    $Key[$co] = $key;
                }
            }
            $co++;
        }
        return $Key;
    }


    /**
     * Ordena arrays multidimensionais,
     * passando o nome da coluna e a ordenação como parametros
     * @params array(), coluna, ordenacao, coluna, ordenacao ...
     *
     * Exemplo:
     * $data[] = array('volume' => 86, 'edition' => 6);
     * $data[] = array('volume' => 67, 'edition' => 7);
     * $sorted = ordenarArrayby($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
     */
    public static function ordenarArrayMultiPorColuna()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    public static function prepararArrayMultiParaJson($dados)
    {
        foreach ($dados as $key => $array) {
            foreach ($array as $key2 => $dado) {
                if (is_array($dado)) {
                    $dado = array_map('strConvertCharset', $dado);
                    $dados[$key][$key2] = array_map('html_entity_decode', $dado);

                    foreach ($dado as $key3 => $dado2) {
                        if (is_array($dado2)) {
                            $dado2 = array_map('strConvertCharset', $dado2);
                            $dados[$key][$key2][$key3] = array_map('html_entity_decode', $dado2);
                        }
                    }
                } else {
                    $dado = strConvertCharset($dado);
                    $dados[$key][$key2] = html_entity_decode($dado);
                }
            }
        }

        return $dados;
    }
}
?>
