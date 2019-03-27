Vue.component('plano-distribuicao-visualizar-produto-resumido', {
    template: `
    <div class="plano-distribuicao-visualizar-produto-resumido " v-if="produto">
        <table class="resumo-distribuicao planoDistribuicao bordered responsive-table" style="margin-bottom: 20px">
            <thead>
                <tr>
                    <th class="center-align" rowspan="2">Quantidade</th>
                    <th colspan="3" class="proponente">Proponente</th>
                    <th colspan="3" class="popular">Preço Popular</th>
                    <th colspan="3" class="gratuito">Distribuição Gratuita</th>
                    <th class="right-align" rowspan="2" width="8%">Receita Prevista total</th>
                </tr>
                <tr class="center-align">
                    <th class="proponente">Qtd. de Inteira</th>
                    <th class="proponente">Qtd. de Meia</th>
                    <th class="proponente">Valor médio</th>
                    <th class="popular">Qtd. de Inteira</th>
                    <th class="popular">Qtd. de Meia</th>
                    <th class="popular">Valor médio</th>
                    <th class="gratuito">Divulgação</th>
                    <th class="gratuito">Patrocinador</th>
                    <th class="gratuito">População</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="right-align">{{produto.QtdeProduzida}}</td>
                    <td class="proponente right-align">{{produto.QtdeVendaNormal}}</td>
                    <td class="proponente right-align">{{produto.QtdeVendaPromocional}}</td>
                    <td class="proponente right-align">{{produto.PrecoUnitarioNormal}}</td>
                    <td class="popular right-align">{{produto.QtdeVendaPopularNormal}}</td>
                    <td class="popular right-align">{{produto.QtdeVendaPopularPromocional}}</td>
                    <td class="popular right-align">{{produto.ReceitaPopularNormal}}</td>
                    <td class="gratuito right-align">{{produto.QtdeProponente}}</td>
                    <td class="gratuito right-align">{{produto.QtdePatrocinador}}</td>
                    <td class="gratuito right-align">{{produto.QtdeOutros}}</td>
                    <td class="right-align">{{produto.Receita}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    `,
    props: {
        'produto': {}
    },
    mixins: [utils]

});
