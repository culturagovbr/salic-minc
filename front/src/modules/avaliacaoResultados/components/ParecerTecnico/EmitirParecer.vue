<template>
    <v-container
        row
        justify-center>
        <v-form v-model="valid">
            <v-dialog
                v-model="dialog"
                full-width
                scrollable
                fullscreen
                transition="dialog-bottom-transition">
                <v-btn
                    slot="activator"
                    color="green"
                    dark>Emitir Parecer</v-btn>
                <v-card>
                    <v-toolbar
                        dark
                        color="primary">
                        <v-btn
                            :href="redirectLink"
                            icon
                            dark>
                            <v-icon>close</v-icon>
                        </v-btn>
                        <v-toolbar-title>Avaliação Financeira - Emissão de Parecer</v-toolbar-title>
                        <v-spacer/>
                    </v-toolbar>

                    <v-card-text>
                        <v-container>
                            <v-card-text>
                                <v-card>
                                    <v-card-title primary-title>
                                        <v-container
                                            pa-0
                                            ma-0>
                                            <div>
                                                <div class="headline">
                                                <b>Projeto:</b> {{ projeto.AnoProjeto }}{{ projeto.Sequencial }} - {{ projeto.NomeProjeto }}</div>
                                                <span class="black--text">
                                                <b>Proponente:</b> {{ proponente.CgcCpf | cnpjFilter }} - {{ proponente.Nome }}</span>
                                            </div>
                                        </v-container>
                                    </v-card-title>
                                    <v-card-text>
                                        <v-container
                                            grid-list-xs
                                            text-xs-center
                                            ma-0
                                            pa-0>
                                            <v-layout
                                                row
                                                wrap>
                                                <v-flex
                                                    xs12
                                                    md6
                                                    mb-2>
                                                    <v-data-table
                                                        :items="[]"
                                                        class="elevation-2"
                                                        hide-headers
                                                        hide-actions
                                                    >
                                                        <template slot="no-data">
                                                            <tr>
                                                                <th colspan="6">Quantidade de Comprovantes</th>
                                                            </tr>
                                                            <tr>
                                                                <td left><b>Total:</b></td>
                                                                <td >{{ consolidacaoComprovantes.qtTotalComprovante }}</td>
                                                                <td left><b>Validados:</b></td>
                                                                <td>
                                                                    <font color="#006400">
                                                                        {{ consolidacaoComprovantes.qtComprovantesValidadosProjeto }} </font></td>
                                                            </tr>
                                                            <tr>
                                                                <td left><b>Não Avaliados:</b></td>
                                                                <td left>{{ consolidacaoComprovantes.qtComprovantesNaoAvaliados }}</td>
                                                                <td left><b>Recusados:</b></td>
                                                                <td left>
                                                                    <font color="red">
                                                                        {{ consolidacaoComprovantes.qtComprovantesRecusadosProjeto }} </font></td>
                                                            </tr>
                                                        </template>
                                                    </v-data-table>
                                                </v-flex>

                                                <v-flex
                                                    xs12
                                                    md6
                                                    mb-4>
                                                    <v-data-table
                                                        :items="[]"
                                                        class="elevation-1"
                                                        hide-headers
                                                        hide-actions
                                                    >
                                                        <template slot="no-data">
                                                            <tr>
                                                                <th colspan="6">Valores Comprovados</th>
                                                            </tr>
                                                            <tr>
                                                                <td left><b>Total:</b></td>
                                                                <td >{{ consolidacaoComprovantes.vlComprovadoProjeto | currency }}</td>
                                                                <td left><b>Recusados:</b></td>
                                                                <td left>
                                                                    <font color="red">
                                                                        {{ consolidacaoComprovantes.vlComprovadoRecusado | currency }}</font></td>
                                                            </tr>
                                                            <tr>
                                                                <td left><b>Validados:</b></td>
                                                                <td>
                                                                    <font color="#006400">
                                                                        {{ consolidacaoComprovantes.vlComprovadoValidado | currency }}</font></td>
                                                            </tr>
                                                        </template>
                                                    </v-data-table>
                                                </v-flex>

                                                <v-flex
                                                    xs4
                                                    d-flex>
                                                    <v-select
                                                        v-model="getParecer.siManifestacao"
                                                        :rules="itemRules"
                                                        :items="items"
                                                        height="20px"
                                                        item-text="text"
                                                        item-value="id"
                                                        label="Manifestação *"
                                                        required="required"
                                                        solo
                                                        append-icon="keyboard_arrow_down"
                                                        full-width
                                                        @change="inputManifestacao($event)"
                                                    />
                                                </v-flex>

                                                <v-flex
                                                    md12
                                                    xs12
                                                    mb-4>
                                                    <v-card>
                                                        <v-responsive>
                                                            <div
                                                                v-show="parecerRules.show"
                                                                class="text-xs-left">
                                                            <h4 :class="parecerRules.color">{{ parecerRules.msg }}*</h4></div>
                                                            <EditorTexto
                                                                :style="parecerRules.backgroundColor"
                                                                :value="getParecer.dsParecer"
                                                                required="required"
                                                                @editor-texto-input="inputParecer($event)"
                                                                @editor-texto-counter="validarParecer($event)"
                                                            />
                                                        </v-responsive>
                                                    </v-card>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-container
                                            grid-list-xs
                                            text-xs-center
                                            ma-0
                                            pa-0>
                                            <v-btn
                                                :disabled="!valid || !parecerRules.enable"
                                                color="primary"
                                                @click.native="salvarParecer()"
                                            >
                                                Salvar
                                            </v-btn>
                                            <v-btn
                                                :href="redirectLink"
                                                :disabled="!valid || !parecerRules.enable"
                                                color="primary"
                                                @click.native="finalizarParecer()"
                                            >
                                                Finalizar
                                            </v-btn>
                                        </v-container>
                                    </v-card-actions>
                                </v-card>
                            </v-card-text>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </v-form>
    </v-container>
</template>

<script>
import Vue from 'vue';
import { mapActions, mapGetters } from 'vuex';
import VueCurrencyFilter from 'vue-currency-filter';
import cnpjFilter from '@/filters/cnpj';
import EditorTexto from '../components/EditorTexto';


Vue.use(VueCurrencyFilter, {
    symbol: 'R$',
    thousandsSeparator: '.',
    fractionCount: 2,
});

export default {
    name: 'EmitirParecer',
    components: {
        EditorTexto,
    },
    filters: {
        cnpjFilter,
    },
    data() {
        return {
            tipo: true,
            idPronac: this.$route.params.id,
            redirectLink: '#/planilha/',
            valid: false,
            dialog: true,
            itemRules: [v => !!v || 'Tipo de manifestação e obrigatório!'],
            parecerRules: {
                show: false,
                color: '',
                backgroundColor: '',
                msg: '',
                enable: false,
            },
            items: [
                {
                    id: 'R',
                    text: 'Reprovação',
                },
                {
                    id: 'A',
                    text: 'Aprovação',
                },
                {
                    id: 'P',
                    text: 'Aprovação com Ressalva',
                },
            ],
            parecerData: { },

        };
    },
    computed: {
        ...mapGetters({
            modalVisible: 'modal/default',
            consolidacaoComprovantes: 'avaliacaoResultados/consolidacaoComprovantes',
            proponente: 'avaliacaoResultados/proponente',
            parecer: 'avaliacaoResultados/parecer',
            projeto: 'avaliacaoResultados/projeto',
            getParecer: 'avaliacaoResultados/parecer',
        }),
    },
    mounted() {
        this.redirectLink = this.redirectLink + this.idPronac;
        this.getConsolidacao(this.idPronac);
        this.validarParecer(this.getParecer.dsParecer);
    },
    methods: {
        ...mapActions({
            requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
            salvar: 'avaliacaoResultados/salvarParecer',
            finalizar: 'avaliacaoResultados/finalizarParecer',
            alterarParecer: 'avaliacaoResultados/alterarParecer',
        }),
        fecharModal() {
            this.modalClose();
        },
        getConsolidacao(id) {
            this.requestEmissaoParecer(id);
        },
        salvarParecer() {
            const data = {
                idPronac: this.idPronac,
                tpAvaliacaoFinanceira: this.tipo,
                siManifestacao: this.getParecer.siManifestacao,
                dsParecer: this.getParecer.dsParecer,
            };
            if (this.parecer.idAvaliacaoFinanceira) {
                data.idAvaliacaoFinanceira = this.parecer.idAvaliacaoFinanceira;
            }

            if (this.parecerData.siManifestacao) {
                data.siManifestacao = this.parecerData.siManifestacao;
            }

            if (this.parecerData.dsParecer) {
                data.dsParecer = this.parecerData.dsParecer;
            }
            this.salvar(data);
        },
        finalizarParecer() {
            const data = {
                idPronac: this.idPronac,
                tpAvaliacaoFinanceira: this.tipo,
                siManifestacao: this.parecer.siManifestacao,
                dsParecer: this.parecer.dsParecer,
                atual: 5,
                proximo: 6,
            };

            if (this.parecer.idAvaliacaoFinanceira) {
                data.idAvaliacaoFinanceira = this.parecer.idAvaliacaoFinanceira;
            }

            if (this.parecerData.siManifestacao) {
                data.siManifestacao = this.parecerData.siManifestacao;
            }

            if (this.parecerData.dsParecer) {
                data.dsParecer = this.parecerData.dsParecer;
            }

            this.finalizar(data);
        },
        inputParecer(e) {
            this.parecerData.dsParecer = e;
            this.validarParecer(e);
        },
        inputManifestacao(e) {
            this.parecerData.siManifestacao = e;
        },
        validarParecer(e) {
            if (e < 10) {
                this.parecerRules = {
                    show: true,
                    color: 'red--text',
                    backgroundColor: { 'background-color': '#FFCDD2' },
                    msg: 'Parecer deve conter mais que 10 characteres',
                    enable: false,
                };
            }
            if (e < 1) {
                this.parecerRules = {
                    show: true,
                    color: 'red--text',
                    backgroundColor: { 'background-color': '#FFCDD2' },
                    msg: 'Parecer é obrigatório!',
                    enable: false,
                };
            }
            if (e >= 10) {
                this.parecerRules = {
                    show: false,
                    color: '',
                    backgroundColor: '',
                    msg: '',
                    enable: true,
                };
            }
        },
    },
};
</script>
