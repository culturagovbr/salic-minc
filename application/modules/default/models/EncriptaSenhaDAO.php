<?php

class EncriptaSenhaDAO extends Zend_Db_Table
{
    /**
     * funcao do banco de dados do sql server no formato PHP.
     *
     * @name encriptaSenha
     * @param $username
     * @param $password
     * @return string
     *
     * @author Ruy Junior Ferreira Silva ruyjfs@gmail.com
     * @since 12/08/2016
     *
     * Exemplo do formado do SQL Server:
     *
     *        BEGIN
     *    DECLARE
     * @w    varchar(30),
     * @s      varchar(15),
     * @t1    int,
     * @t2    int,
     * @k      int,
     * @i      int,
     * @j      int,
     * @f      int,
     * @v      int
     *
     *    SET @w = RTRIM(LTRIM(@p_senha))
     *    SET @t1 = LEN(RTRIM(LTRIM(@p_identificacao)))
     *    SET @t2 = LEN(@w)
     *    IF @t2 < 1
     *    BEGIN
     *        SET @p_senha = '??????'
     *        SET @w = '??????'
     *        SET @t2 = 6
     *    END
     *    WHILE LEN(@w) < 15
     *    BEGIN
     *        SET @w = @w + @w
     *    END
     *    SET @w = SUBSTRING(@w, 1, 15)
     *    SET @k = ASCII(SUBSTRING(@w, 1, 1)) + 2
     *    SET @s = ''
     *    SET @i = 0
     *    WHILE @i < 15
     *    BEGIN
     *        SET @i = @i + 1
     *        SET @v = (@t1 + @t2) * @k / @i
     *        SET @f = ASCII(SUBSTRING(@w, 1, 1))
     *        SET @w = SUBSTRING(@w, 2, 15)
     *        SET @j = ((@f * @k) + @t1 + (@t2 * @f)) / @i
     *        SET @v = @v + @j
     *        IF @v < 33
     *        BEGIN
     *            SET @v = @v + (@t1 * @i)
     *        END
     *        SET @j = @v % 94
     *        SET @s = @s + CHAR(33 + @j)
     *    END
     *    RETURN @s
     */
    public static function encriptaSenha2($username, $password)
    {
        $w = trim($password);
        $t1 = strlen(trim($username));
        $t2 = strlen($w);

        if ($t2 < 1) {
            $password = '??????';
            $w = '??????';
            $t2 = 6;
        }

        while (strlen($w) < 15) {
            $w = $w . $w;
        }

        $w = substr($w, 1, 15);
        $k = ord((substr($w, 1, 1))) + 2;
//        $k = chr(ord((substr($w, 1, 1)))) . 2;
        $s = '';
        $i = 0;

        while ($i < 15) {
            $i = $i + 1;
            $v = ($t1 + $t2) * $k / $i;
            $f = ord(substr($w, 1, 1));
//            $f = chr(ord(substr($w, 1, 1)));
            $w = substr($w, 2, 15);
            $j = (($f * $k) + $t1 + ($t2 * $f)) / $i;
            $v = $v + $j;
            if ($v < 33) {
                $v = $v + ($t1 * $i);
            }
            $j = $v % 94;
            $s = $s . (33 + $j);
        }

        return $s;
    }

    public static function encriptaSenha($cpf, $senha)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        if ($db instanceof Zend_Db_Adapter_Pdo_Mssql) {
            $sql = "SELECT tabelas.dbo.fnEncriptaSenha('" . $cpf . "', '$senha' ) as senha";
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $result = $db->fetchRow($sql);
            return ($result)? $db->fetchRow($sql)->senha : '';
        } else {
            return md5($senha);
        }
    }
}
