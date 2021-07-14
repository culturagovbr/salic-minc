<?php

namespace MinC\Assinatura\Servico;

interface IDocumentoAssinatura
{
    function __construct($idPronac, $idTipoDoAtoAdministrativo, $idAtoDeGestao = null);

    function criarDocumento();

    function iniciarFluxo() : int;
}