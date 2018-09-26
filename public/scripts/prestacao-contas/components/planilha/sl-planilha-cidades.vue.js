Vue.component('sl-planilha-cidades', {
    props: ['cidade', 'cdEtapa', 'uf', 'cdProduto', 'idpronac', "documento"],
    template: `
        <li class="active">
            <div class="collapsible-header blue-text active" style="padding-left: 70px;">
                <i class="material-icons">place</i>
                {{ cidade.cidade }}
            </div>
            <div class="collapsible-body no-padding margin20 scroll-x">
                <slot :cidade="cidade" :produto="cdProduto" :uf="uf" :etapa="cdEtapa">
                    <sl-planilha-itens
                        :itens="cidade.itens"
                        :idpronac="idpronac"
                        :uf="uf"
                        :cdproduto="cdProduto"
                        :cdcidade="cidade.cdCidade"
                        :cdetapa="cdEtapa"
                    ></sl-planilha-itens>
                </slot>
            </div>
        </li>
    `,
    methods: {
        iniciarCollapsible: function () {
            $3('.collapsible').each(function() {
                $3(this).collapsible();
            });
        },
        isObject: function (el) {

            return typeof el === "object";

        },
        converterParaReal: function (value) {
            value = parseFloat(value);
            return numeral(value).format('0,0.00');
        }
    }
})