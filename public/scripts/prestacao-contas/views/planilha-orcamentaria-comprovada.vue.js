Vue.component('planilha-orcamentaria-comprovada', {
    props: ['idpronac', 'documento'],
    template: `
        <div>
            <sl-planilha-produtos 
                :documento="documento" 
                :produtos="produtos" 
                :idpronac="idpronac"
            >
                  <template slot-scope="slot">
                        <sl-planilha-etapas
                            v-for="(etapa, index) in slot.produto.etapa"
                            :etapa="etapa"
                            :idpronac="idpronac"
                            :cdProduto="slot.produto.cdProduto"
                            :key="index"
                            :documento="documento"
                        >
                          <template slot-scope="slot">
                                <sl-planilha-ufs
                                    v-for="(estado, index) in slot.etapa.UF"
                                    :key="index"
                                    :idpronac="idpronac"
                                    :estado="estado"
                                    :cdEtapa="slot.etapa.cdEtapa"
                                    :cdProduto="slot.produto"
                                    :documento="documento"
                                >
                                  <template slot-scope="slot">
                                    <sl-planilha-cidades
                                        v-for="(cidade, index) in slot.estado.cidade"
                                        :cidade="cidade"
                                        :idpronac="idpronac"
                                        :uf="slot.estado.Uf"
                                        :cdEtapa="slot.etapa"
                                        :cdProduto="slot.produto"
                                        :key="slot.estado.cdUf"
                                        :id="slot.estado.cdUf"
                                        :documento="documento"
                                    >
                                      <template slot-scope="slot">
                                        <div class="row">
                                            <div class="col s12">
                                                <ul class="tabs">
                                                  <li class="tab col s3">
                                                    <a class="active" :href="'#test_1_' + slot.cidade.cdCidade +'_' + slot.produto +'_'+ slot.etapa">
                                                        Todos
                                                    </a>
                                                  </li>
                                                  <li class="tab col s3">
                                                    <a :href="'#test_2_' + slot.cidade.cdCidade +'_'+ slot.produto+'_'+ slot.etapa">
                                                        Aguardando Analise</a>
                                                  </li>
                                                  <li class="tab col s3">
                                                    <a :href="'#test_3_' + slot.cidade.cdCidade +'_'+ slot.produto+'_'+ slot.etapa">
                                                        Avaliado
                                                    </a>
                                                  </li>
                                                  <li class="tab col s3">
                                                    <a :href="'#test_4_' + slot.cidade.cdCidade +'_'+ slot.produto+'_'+ slot.etapa">
                                                        Impugnados
                                                    </a>
                                                  </li>
                                                </ul>
                                            </div>
                                            <div :id="'test_1_'+ slot.cidade.cdCidade +'_'+ slot.produto+'_'+ slot.etapa" class="col s12">
                                                <sl-planilha-itens
                                                    :itens="slot.cidade.itens['todos']"
                                                    :idpronac="idpronac"
                                                    :uf="slot.uf"
                                                    :cdproduto="slot.produto"
                                                    :cdcidade="slot.cidade.cdCidade"
                                                    :cdetapa="slot.etapa"
                                                    tecnico="true"
                                                    :documento="documento"
                                                ></sl-planilha-itens>
                                            </div>
                                            <div :id="'test_2_' + slot.cidade.cdCidade +'_'+ slot.produto+'_'+ slot.etapa" class="col s12">
                                                <sl-planilha-itens
                                                    :itens="slot.cidade.itens[4]"
                                                    :idpronac="idpronac"
                                                    :uf="slot.uf"
                                                    :cdproduto="slot.produto"
                                                    :cdcidade="slot.cidade.cdCidade"
                                                    :cdetapa="slot.etapa"
                                                    stitemavaliado="4"
                                                    :documento="documento"
                                                ></sl-planilha-itens>
                                            </div>
                                            <div :id="'test_3_' + slot.cidade.cdCidade +'_'+ slot.produto+'_'+ slot.etapa" class="col s12">
                                                <sl-planilha-itens
                                                    :itens="slot.cidade.itens[1]"
                                                    :idpronac="idpronac"
                                                    :uf="slot.uf"
                                                    :cdproduto="slot.produto"
                                                    :cdcidade="slot.cidade.cdCidade"
                                                    :cdetapa="slot.etapa"
                                                    stitemavaliado="1"
                                                    :documento="documento"
                                                ></sl-planilha-itens>
                                            </div>
                                            <div :id="'test_4_' + slot.cidade.cdCidade +'_'+ slot.produto+'_'+ slot.etapa" class="col s12">
                                                <sl-planilha-itens
                                                    :itens="slot.cidade.itens[3]"
                                                    :idpronac="idpronac"
                                                    :uf="slot.uf"
                                                    :cdproduto="slot.produto"
                                                    :cdcidade="slot.cidade.cdCidade"
                                                    :cdetapa="slot.etapa"
                                                    stitemavaliado="3"
                                                    :documento="documento"
                                                ></sl-planilha-itens>
                                            </div>
                                        </div>
                                      </template>
                                    </sl-planilha-cidades>
                                  </template>
                                </sl-planilha-ufs>
                          </template>
                        </sl-planilha-etapas>
                  </template>
            </sl-planilha-produtos>
        </div>
    `,
    mounted: function () {
        let vue = this;
        $3.ajax({
            url: "/prestacao-contas/realizar-prestacao-contas/planilha-analise-filtros/idPronac/" + this.idpronac
        }).done(function( data ) {
            vue.$data.produtos = data;
        });
    },
    data: function () {
        return {
            produtos: []
        };
    },
    methods: {
        iniciarCollapsible: function () {
            $3('.collapsible').each(function() {
                $3(this).collapsible();
            });

            $3(document).ready(function(){
                $3('ul.tabs').tabs();
            });
        }
    }
})
