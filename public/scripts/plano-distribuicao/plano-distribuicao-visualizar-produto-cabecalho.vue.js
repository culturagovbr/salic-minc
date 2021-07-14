Vue.component('plano-distribuicao-visualizar-produto-cabecalho', {
    template: `
    <div class="plano-distribuicao-visualizar-produto-cabecalho card" v-if="produto">
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
    </div>
    `,
    props: {
        'produto': {}
    },
    mixins: [utils]
});
