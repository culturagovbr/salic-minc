<?php

namespace MinC\Servico;

interface IServicoRestZend extends IServicoRest
{
    function __construct($request, $response);
}
