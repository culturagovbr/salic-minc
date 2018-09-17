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
     * passando o nome da coluna e a ordenacao como parametros
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
    
    // Based on https://secure.php.net/manual/en/function.utf8-encode.php#109965
    public static function utf8EncodeArray($input) {
        
        if (is_string($input)) {
            return utf8_encode($input);
        } else if (is_array($input)) {
            
            $arrIter = [];
            
            foreach ($input as $key => $value) {
                $key = utf8_encode($key);
                
                if (is_string($value)) {
                    $value = utf8_encode($value);
                } else if (is_array($value)) {
                    $value = self::utf8EncodeArray($value);
                }
                $arrIter[$key] = $value;
            }
            return $arrIter;
        }
    }
}
?>
