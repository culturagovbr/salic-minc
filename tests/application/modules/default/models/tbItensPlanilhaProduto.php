<?php
/**
 * Default_tbItensPlanilhaProduto
 *
 * @package
 */
class tbItensPlanilhaProdutoModelTest extends MinC_Test_ModelTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Marcado para refatoração futura
        // * fixture do banco de dados com dados controlados
        $this->idPronac = '206025';
    }

    public function testItensAtuaisBlocoReadequacaoPronac()
    {
        $idEtapa = 1;
        $idProduto = 51;
        $idMunicipio = 431490;
        
        $tbItensPlanilhaProduto = new tbItensPlanilhaProduto();
        $result = $tbItensPlanilhaProduto->itensPorProdutoItemEtapaMunicipioReadequacao(
            $idEtapa,
            $idProduto,
            $idMunicipio,
            $this->idPronac
        );
        
        $expectedItens = [35, 3235, 1037, 1044, 2692, 53];
        
        $itens = array_values((array)$result)[0];
        
        $this->assertEquals(
            count($expectedItens),
            count($itens)
        );
        
        $intersect = array_intersect(
            $expectedItens,
            array_column($itens, 'idItem')
        );

        $diff = array_diff(array_column($itens, 'idItem'), $expectedItens);
        
        $this->assertEmpty($diff);
        
        $itensTotais = $tbItensPlanilhaProduto->itensPorItemEEtapaReadequacao($idEtapa, $idProduto);
        
        $itensAtuais = $tbItensPlanilhaProduto->itensPorProdutoItemEtapaMunicipioReadequacao(
            $idEtapa,
            $idProduto,
            $idMunicipio,
            $this->idPronac
        );

        $a = 0;
        $itensArray = array();
        foreach ($itensTotais as $iT) {
            $itensArray[$a]['idPlanilhaItens'] = $iT->idPlanilhaItens;
            $itensArray[$a]['Item'] = utf8_encode($iT->Item);
            $a++;
        }
        
        $itensAtuaisArray = array();
        $i = 0;
        foreach ($itensAtuais as $ia) {
            $itensAtuaisArray[$i]['idPlanilhaItens'] = $ia->idPlanilhaItens;
            $itensAtuaisArray[$i]['Item'] = utf8_encode($ia->Item);
            $i ++;
        }
        
        $this->assertEquals(
            count(
                $itensArray) - count($itensAtuaisArray
                ),
            count(
                array_diff_assoc(
                    $itensArray,
                    $itensAtuaisArray
                )
            )
        );
        
    }
}