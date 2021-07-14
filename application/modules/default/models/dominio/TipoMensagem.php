<?php

/**
 * Classe para o dominio da informa��o de tipo de Mensagens.
 *
 */
class Dominio_TipoMensagem
{
    const DILIGENCIA = 1;

    const CAPTACAO = 2;

    /**
     * Lista de tipos de mensagens.
     *
     * @var array
     */
    private static $lista = array(
        self::DILIGENCIA => 'Dilig�ncia',
        self::CAPTACAO => 'Capita��o'
    );

    public static function getLista()
    {
        return self::$lista;
    }

    /**
     * Busca a Descrição de acordo com o n�mero do tipo.
     *
     * @param integer $numero
     * @return string
     */
    public static function getDescricao($numero)
    {
        $descricao = null;
        if ($numero) {
            $descricao = self::$lista[$numero];
        }

        return $descricao;
    }

    /**
     * Classe para o dominio da informa��o de tipo de Mensagens.
     *
     */
    public function __construct()
    {
    }
}
