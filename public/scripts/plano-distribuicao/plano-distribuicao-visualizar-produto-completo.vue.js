Vue.component('plano-distribuicao-visualizar-produto-completo', {
    template: `
    <div class="plano-distribuicao-visualizar-produto-completo card" v-if="produto">
        <table class="bordered">
            <thead>
               <tr>
                    <th>&Aacute;rea</th>
                    <th>Segmento</th>
                    <th>Principal</th>
                    <th>Canal aberto?</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{produto.DescricaoArea}}</td>
                    <td>{{produto.DescricaoSegmento}}</td>
                    <td>{{label_sim_ou_nao(produto.stPrincipal)}}</td>
                    <td>{{label_sim_ou_nao(produto.canalAberto)}}</td>
                </tr>
            </tbody>
        </table>
        <table class="bordered">
            <thead>
                <tr>
                    <th colspan="3" class="center-align gratuito padding10">Quantidade Distribui&ccedil;&atilde;o Gratuita</th>
                </tr>
                <tr>
                    <td class="gratuito">Divulga&ccedil;&atilde;o</td>
                    <td class="gratuito">Patrocinador</td>
                    <td class="gratuito">Popula&ccedil;&atilde;o</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="gratuito">{{produto.QtdeProponente}}</td>
                    <td class="gratuito">{{produto.QtdePatrocinador}}</td>
                    <td class="gratuito">{{produto.QtdeOutros}}</td>
                </tr>
            </tbody>
        </table>
        <table class="bordered">
            <thead>
                <tr>
                    <th colspan="3" class="center-align popular padding10">Pre&ccedil;o Popular</th>
                </tr>
                <tr>
                    <td class="popular">Quantidade Inteira</td>
                    <td class="popular">Quantidade Meia</td>
                    <td class="popular">Valor m&eacute;dio</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="popular">{{produto.QtdeVendaPopularNormal}}</td>
                    <td class="popular">{{produto.QtdeVendaPopularPromocional}}</td>
                    <td class="popular">{{produto.ReceitaPopularNormal}}</td>
                </tr>
            </tbody>
        </table>
        <table class="bordered">
            <thead>
                <tr>
                    <th colspan="3" class="center-align proponente padding10">Proponente</th>
                </tr>
                <tr>
                    <td class="proponente">Quantidade Inteira</td>
                    <td class="proponente">Quantidade Meia</td>
                    <td class="proponente">Valor m&eacute;dio</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="proponente">{{produto.QtdeVendaNormal}}</td>
                    <td class="proponente">{{produto.QtdeVendaPromocional}}</td>
                    <td class="proponente">{{produto.PrecoUnitarioNormal}}</td>
                </tr>
            </tbody>
        </table>
        <table class="bordered">
            <thead>
               <tr>
                    <th class="center-align">Quantidade Total</th>
                    <th class="center-align">Receita Prevista Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="right-align">{{produto.QtdeProduzida}}</td>
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
