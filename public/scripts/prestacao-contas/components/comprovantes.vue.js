Vue.component('comprovantes', {
    template: `
        <div>
            <template v-if="!loading">
                <template v-if="dados.length > 0 || Object.keys(dados).length > 0 ">
                  <transition-group
                    tag="ul"
                    class="collapsible"
                    name="list"
                    data-collapsible="accordion"
                    enter-active-class="animated fadeIn"
                    leave-active-class="animated fadeOut"
                  >
                    <li
                        v-for="dado in dados"
                        :key="dado.idComprovantePagamento"
                    >
                      <div class="collapsible-header">
                        Fornecedor: {{dado.fornecedor.nome}} - R$ {{valorFormatado(dado.valor)}}
                        <span :class="['badge white-text ', badgeStatus(dado.status)]">{{dado.status}}</span>
                      </div>
                      <div :class="['collapsible-body lighten-2', badgeCSS(dado.stItemAvaliado)]">
                            <div class="card">
                                <div class="card-content">
                                    <template v-if="!formVisivel" >
                                        <template v-if="(tipo == 'nacional')" >
                                            <comprovante-table :dados="dado"></comprovante-table>
                                        </template>
                                        <template v-if="(tipo == 'internacional')" >
                                            <sl-comprovante-internacional-table :dados="dado">
                                            </sl-comprovante-internacional-table>
                                        </template>
                                    </template>
                                    <div class="row">
                                    <div class="col s12">
                                    <div class="center-align ">
                                        <button
                                            v-if="!formVisivel"
                                            @click="mostrarForm()"
                                            class="btn blue"
                                        >
                                            <i class="material-icons left">
                                                edit
                                            </i>
                                            editar
                                        </button>
                                        <button v-if="!formVisivel" type="button" class="btn red white-text center-align"
                                            @click.prevent="excluir(dado.idComprovantePagamento, dado.idArquivo, dado.idComprovantePagamento)">
                                            <i class="material-icons left">
                                                delete_forever
                                            </i>
                                            excluir</button>
                                    </div>
                                    </div>
                                    </div>
                                    <template v-if="formVisivel">
                                        <component
                                            :is="componenteform"
                                            :dados="dado"
                                            :index="dado.idComprovantePagamento"
                                            url="/prestacao-contas/gerenciar/atualizar"
                                            tipoform="edicao"
                                            :item="idplanilhaitem"
                                            :datainicio="datainicio"
                                            :datafim="datafim"
                                            :valoraprovado="parseFloat(valoraprovado)"
                                            :valorcomprovado="parseFloat(valorComprovado)"
                                            :valorantigo="parseFloat(dado.valor)"
                                        >
                                        </component>
                                    </template>
                                </div>
                            </div>
                      </div>
                    </li>
                  </transition-group>
                </template>
                <template v-else>
                    <p> Sem comprovantes</p>
                </template>
            </template>
            <template v-else>
                <div class="preloader-wrapper small active">
                    <div class="spinner-layer spinner-green-only">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div><div class="gap-patch">
                            <div class="circle"></div>
                        </div><div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    `,
    components:{
        'comprovante-table': comprovanteTable,
    },
    props: {
        idpronac: null,
        produto: null,
        stitemavaliado: null,
        uf: null,
        idUf: null,
        idmunicipio: null,
        idplanilhaitem: null,
        etapa: null,
        componenteform: null,
        tipo: null,
        url: null,
        datainicio: null,
        datafim: null,
        valoraprovado: null,
        valorcomprovado: null
    },
    created() {
        let vue = this;
        this.$root.$on('novo-comprovante-nacional', function(data) {
            if(vue.tipo =='nacional'){
                data.status='novo';
                if (Object.values(vue.$data.dados).length == 0) {
                    let a = new Object;
                    a[data._index]  = data;
                    vue.$data.dados = a;
                } else {
                    Vue.set(vue.$data.dados, data._index, data);
                }
                vue.valorComprovado = parseFloat(vue.valorcomprovado) + parseFloat(data.valor);
            }
        })

        this.$root.$on('atualizado-comprovante-nacional', function(data) {
            vue.formVisivel = false;
            if(vue.tipo =='nacional'){
                data.status='atualizado';
                Vue.set(vue.$data.dados, data._index, data);
            }
            vue.valorComprovado = (parseFloat(vue.valorcomprovado) - parseFloat(data.valorAntigo)) + parseFloat(data.valor);
        })

        this.$root.$on('cancelar-comprovante-nacional', function(data) {
            if(vue.tipo =='nacional'){
                vue.formVisivel = false;
            }
        })

        this.$root.$on('novo-comprovante-internacional', function(data) {
            if(vue.tipo =='internacional'){
                data.status='novo';
                if (Object.values(vue.$data.dados).length == 0) {
                    let a = new Object;
                    a[data._index]  = data;
                    vue.$data.dados = a;
                } else {
                    Vue.set(vue.$data.dados, data._index, data);
                }
                vue.valorComprovado = parseFloat(vue.valorcomprovado) + parseFloat(data.valor);
            }
        })

        this.$root.$on('atualizado-comprovante-internacional', function(data) {
            if(vue.tipo =='internacional'){
                data.status='atualizado';
                vue.formVisivel = false;
                Vue.set(vue.$data.dados, data._index, data);
            }
            vue.valorComprovado = (parseFloat(vue.valorcomprovado) - parseFloat(data.valorAntigo)) + parseFloat(data.valor);
        })

        this.$root.$on('cancelar-comprovante-internacional', function(data) {
            if(vue.tipo =='internacional'){
                vue.formVisivel = false;
            }
        })
    },
    mounted: function() {
        let vue = this;
        url = '/prestacao-contas/comprovante-pagamento';
        $3.ajax({
            type: "GET",
            url:url,
            data:{
                idPronac: this.idpronac,
                idPlanilhaItem: this.idplanilhaitem,
                produto: this.produto,
                uf: this.uf,
                idUf: this.idUf,
                idmunicipio: this.idmunicipio,
                etapa: this.etapa,
                tipo: vue.tipo
            },
            beforeSend: function() {
                vue.loading = true;
            },
            complete: function(){
                vue.loading = false;
            }
        })
        .done(function(data) {
            if (data.data.length != 0) {
                vue.$data.dados = data.data;
            }

            $3('.collapsible').each(function() {
                $3(this).collapsible();
            });
        })
        .fail(function(jqXHR) {
            alert('error');
        });
    },
    computed: {
    },
    updated() {
        $3('.collapsible').each(function() {
            $3(this).collapsible();
        });
    },
    methods:{
        badgeCSS: function(id) {
            if (id == 1) {
                return {
                    'green ': true,
                }
            }
            if (id == 3) {
                return {
                    ' red': true,
                }
            }
            if (id == 4) {
                return {
                    'grey': true,
                }
            }
        },
        situacao: function(id) {
            estado = null;
            switch(parseInt(id)) {
                case 1:
                     estado = 'Aprovado';
                    break;
                case 3:
                    estado =  'Recusado'
                    break;
                default:
                    estado =  'N\xE3o avaliado';
            }
            return estado;
        },
        badgeStatus: function(id) {
            if (id == 'novo') {
                return {
                    ' green accent-2': true,
                }
            }

            if (id == 'atualizado') {
                return {
                    'blue': true,
                }
            }
        },
        mostrarForm: function() {
            this.formVisivel = true;
        },
        excluir: function(id, idArquivo, index) {
            this.$root.$emit('excluir-comprovante-nacional', this.dados[index]);
            this.$delete(this.dados, index);

            var vue = this;
            url = '/prestacao-contas/gerenciar/excluir';
            $3.ajax({
              type: "POST",
              url:url,
              data:{
                  comprovante: {idComprovantePagamento: id},
              }
            })
            .done(function(data) {
                Materialize.toast('Excluido com sucesso!', 4000, 'red');
            })
            .fail(function(jqXHR) {
                alert('error');
            });
        },
        valorFormatado: function(valor) {
            return numeral(parseFloat(valor)).format('0,0.00');
        }
    },
    data: function(){
        return {
            dados: {},
            formVisivel: false,
            valorComprovado: this.valorcomprovado,
            loading: false
        }
    }
});
