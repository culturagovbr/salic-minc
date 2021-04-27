Vue.component('plano-distribuicao-detalhamentos-listagem', {
    template: `
        <div class="row center-align">
            <table class="bordered">
                <thead v-if="detalhamentos && detalhamentos.length > 0">
                    <tr>
                        <th rowspan="2">Categoria</th>
                        <th rowspan="2" class="center-align">Quantidade</th>
                        <th colspan="3" class="proponente center-align">Proponente</th>
                        <th colspan="3" class="popular center-align">Pre&ccedil;o Popular</th>
                        <th rowspan="2" class="gratuito center-align">Distribui&ccedil;&atilde;o <br>Gratuita</th>
                        <th rowspan="2" class="center-align">Receita <br> Prevista</th>
                        <th v-if="!disabled" rowspan="2" colspan="2" width="12%" class="center-align">A&ccedil;&otildees</th>
                    </tr>
                    <tr>
                        <th class="proponente center-align">Qtd. Inteira</th>
                        <th class="proponente center-align">Qtd. Meia</th>
                        <th class="proponente center-align">Pre&ccedil;o <br> Unitario</th>
                        <th class="popular center-align">Qtd. Inteira</th>
                        <th class="popular center-align">Qtd. Meia</th>
                        <th class="popular center-align">Pre&ccedil;o <br> Unitario</th>
                    </tr>
                </thead>
                <tbody v-if="detalhamentos && detalhamentos.length > 0">
                    <tr v-for="( detalhamento, index ) in detalhamentos" 
                        :key="detalhamento.idDetalhaPlanoDistribuicao"
                        :id="detalhamento.idDetalhaPlanoDistribuicao"
                        :class="definirClassesDaLinha(detalhamento)">
                        <td>{{ detalhamento.dsProduto }}</td>
                        <td class="center-align">{{ detalhamento.qtExemplares }}</td>
                        <!--Preço Proponente -->
                        <td class="center-align">{{ detalhamento.qtProponenteIntegral }}</td>
                        <td class="center-align">{{ detalhamento.qtProponenteParcial }}</td>
                        <td class="right-align">{{ formatarValor(detalhamento.vlUnitarioProponenteIntegral) }}</td>
                        <!--Preço Popular -->
                        <td class="center-align">{{ detalhamento.qtPopularIntegral }}</td>
                        <td class="center-align">{{ detalhamento.qtPopularParcial }}</td>
                        <td class="right-align">{{ formatarValor(detalhamento.vlUnitarioPopularIntegral) }}</td>
                        <!-- Distribuicao Gratuita-->
                        <td class="center-align">
                            {{ 
                                parseInt(detalhamento.qtGratuitaDivulgacao) +
                                parseInt(detalhamento.qtGratuitaPatrocinador) + 
                                parseInt(detalhamento.qtGratuitaPopulacao) 
                            }}
                        </td>
                        <td class="right-align">{{ formatarValor(detalhamento.vlReceitaPrevista) }}</td>
                        <td colspan="2" v-if="!disabled">
                            <component
                                v-bind:is="componenteDetalhamento"
                                :disabled="disabled"
                                :detalhamento="detalhamento"
                                :index="index"
                                v-on:eventoBotao="emitirEvento"
                            ></component>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="12" class="center-align">Nenhum detalhamento cadastrado</td>
                    </tr>
                </tbody>
                <plano-distribuicao-detalhamentos-consolidacao
                    :detalhamentos="detalhamentos"
                    :disabled="disabled"
                ></plano-distribuicao-detalhamentos-consolidacao>
            </table>
            <table style="max-width: 300px; margin: 0;" v-if="detalhamentos && detalhamentos.length > 0">
                <tr>
                    <th><b>Valor m&eacute;dio </b></th>
                    <td v-if="((valorMedioProponente.value() > 225) && (this.canalaberto == 0))" class="center-align red"> 
                        {{valorMedioProponenteFormatado}}
                    </td>
                    <td v-else class="center-align ">{{valorMedioProponenteFormatado}}</td>
                </tr>
            </table>
        </div>
    `,
    props: {
        'disabled': false,
        'canalaberto': null,
        'local': {},
        'detalhamentos': {},
        'componenteDetalhamento': {
            default: 'detalhamento-botoes-padroes',
            type: String
        }
    },
    mixins: [utils],
    watch: {
        detalhamentos: function () {
            if ((numeral(this.valorMedioProponente).value() > 225
                && (this.canalaberto == 0))) {
                this.mensagemAlerta("O valor medio:" + this.valorMedioProponenteFormatado + ", n\xE3o pode ultrapassar: 225,00");
                this.$data.detalhamentos.splice(-1, 1)
            }
        }
    },
    computed: {
        valorMedioProponente: function () {
            var vlReceitaProponenteIntegral = numeral();
            var vlReceitaProponenteParcial = numeral();
            var qtProponenteIntegral = numeral();
            var qtProponenteParcial = numeral();

            for (var i = 0; i < this.detalhamentos.length; i++) {
                vlReceitaProponenteIntegral.add(this.detalhamentos[i]['vlReceitaProponenteIntegral']);
                vlReceitaProponenteParcial.add(parseFloat(this.detalhamentos[i]['vlReceitaProponenteParcial']));
                qtProponenteIntegral.add(parseFloat(this.detalhamentos[i]['qtProponenteIntegral']));
                qtProponenteParcial.add(parseFloat(this.detalhamentos[i]['qtProponenteParcial']));
            }

            let media = numeral(parseFloat(vlReceitaProponenteIntegral.value() + vlReceitaProponenteParcial.value()) / (qtProponenteIntegral.value() + qtProponenteParcial.value()));

            return media;
        },
        valorMedioProponenteFormatado: function () {
            return this.valorMedioProponente.format();
        }
    },
    methods: {
        emitirEvento: function (callback, detalhamento, index) {
            this.$emit('eventoListagem', callback, detalhamento, index);
        },
        definirClassesDaLinha(detalhamento) {
            return {
                'linha-excluida': (detalhamento.tpSolicitacao && detalhamento.tpSolicitacao == 'E'),
                'linha-incluida': (detalhamento.tpSolicitacao && detalhamento.tpSolicitacao == 'I'),
                'linha-atualizada': (detalhamento.tpSolicitacao && detalhamento.tpSolicitacao == 'A')
            }
        }
    }
});

Vue.component('detalhamento-botoes-padroes', {
    template: `
    <div style="width: 100%;" class="center-align">
         <a v-if="!mostrarBotaoRestaurarItem"
            href="javascript:void(0)"
            class="btn small waves-effect waves-light btn-primary btn-editar"
            :class="_uid + '_teste'"
            title="Editar detalhamento"
            v-bind:disabled="disabled"
            @click.prevent="emitirEvento('editarItem', detalhamento, index)">
            <i class="material-icons">edit</i>
        </a>
        <a v-if="!mostrarBotaoRestaurarItem"
            href="javascript:void(0)"
            class="btn small waves-effect waves-light btn-danger btn-excluir-item"
            title="Excluir detalhamento"
            v-bind:disabled="disabled"
            @click.prevent="emitirEvento('excluirItem', detalhamento, index)">
            <i class="material-icons">delete</i>
        </a>
        <a v-if="mostrarBotaoRestaurarItem"
            href="javascript:void(0)"
            class="btn small waves-effect waves-light btn-default btn-excluir-item"
            title="Restaurar item"
            v-bind:disabled="disabled"
                @click.prevent="emitirEvento('restaurarItem', detalhamento, index)">
            <i class="material-icons">settings_backup_restore</i>
        </a>
    </div>
    `,
    props: [
        'disabled',
        'index',
        'detalhamento'
    ],
    computed: {
        mostrarBotaoRestaurarItem: function () {
            if (this.detalhamento.tpSolicitacao && this.detalhamento.tpSolicitacao == 'E') {
                return true;
            }

            return false;
        }
    },
    methods: {
        emitirEvento: function (callbackParent, detalhamento, index) {
            this.$emit('eventoBotao', callbackParent, detalhamento, index);
        }
    }
});