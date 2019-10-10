<template>
    <v-layout
        row
        justify-center>
        <v-dialog
            :value="isModalVisible === 'avaliacao-item'"
            scrollable
            fullscreen
            transition="dialog-bottom-transition"
            hide-overlay
        >
            <v-snackbar
                v-model="snackbarAlerta"
            >
                {{ snackbarTexto }}
                <v-btn
                    color="pink"
                    flat
                    @click="snackbarAlerta = false"
                >
                    Fechar
                </v-btn>
            </v-snackbar>
            <v-card>
                <v-toolbar
                    dark
                    color="primary">
                    <v-btn
                        icon
                        dark
                        @click.native="fecharModal">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Avaliar comprovantes
                        <span v-if="item">item <b>"{{ item.item }}"</b></span>
                    </v-toolbar-title>
                </v-toolbar>

                <v-card-text>
                    <v-subheader>Dados da Comprovação</v-subheader>
                    <v-container
                        fluid
                        grid-list-md
                        class="pa-10 elevation-0">
                        <v-layout wrap>
                            <v-flex
                                xs12
                                sm6
                                md4>
                                <b>Produto:</b> {{ descricaoProduto }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4>
                                <b>Etapa:</b> {{ descricaoEtapa }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4>
                                <b>Item de Custo:</b> {{ item.item }}
                            </v-flex>
                        </v-layout>
                        <v-divider class="my-2"/>
                        <v-layout wrap>
                            <v-flex
                                xs12
                                sm6
                                md4>
                                <b>Valor Aprovado:</b> {{ item.varlorAprovado | moeda }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4>
                                <b>Valor Comprovado:</b> {{ item.varlorComprovado | moeda }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4>
                                <b>Comprovação Validada:</b> {{ valorComprovacaoValidada | moeda }}
                            </v-flex>
                        </v-layout>
                    </v-container>

                    <lista-de-comprovantes :comprovantes="comprovantes">
                        <template
                            slot="slot-comprovantes"
                            slot-scope="{ props }">
                            <v-form
                                ref="form"
                                v-model="valid"
                                lazy-validation
                            >
                                <v-layout
                                    row
                                    wrap>
                                    <v-flex xs12>
                                        <b>Avaliação</b>
                                        <v-radio-group
                                            ref="stItemAvaliado"
                                            v-model="props.stItemAvaliado"
                                            :rules="[rules.required, rules.avaliacao]"
                                            name="stItemAvaliado"
                                            type="radio"
                                            row
                                        >
                                            <v-radio
                                                label="Aprovado"
                                                value="1"
                                                name="stItemAvaliadoModel"
                                                color="green"/>
                                            <v-radio
                                                label="Reprovado"
                                                value="3"
                                                name="stItemAvaliadoModel"
                                                color="red"/>
                                        </v-radio-group>
                                    </v-flex>
                                    <v-flex xs12>
                                        <v-textarea
                                            v-model="props.dsOcorrenciaDoTecnico"
                                            :rules="[rules.parecer]"
                                            auto-grow
                                            box
                                            label="Parecer"
                                            autofocus
                                        />
                                    </v-flex>
                                </v-layout>
                                <v-container
                                    grid-list-xs
                                    text-xs-center
                                    ma-0
                                    pa-0>
                                    <v-btn
                                        :disabled="!valid"
                                        :loading="loading"
                                        @click="salvarAvaliacao(props)"
                                    >
                                        <v-icon
                                            left
                                            dark>save</v-icon>
                                        Salvar
                                    </v-btn>
                                </v-container>
                            </v-form>
                        </template>
                    </lista-de-comprovantes>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ListaDeComprovantes from '@/modules/avaliacaoResultados/components/components/ListaDeComprovantes';

export default {
    name: 'AnalisarItem',
    components: { ListaDeComprovantes },
    filters: {
        moeda: (moedaString) => {
            const moeda = Number(moedaString);
            return moeda.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
        },
    },
    props: {
        item: { type: Object, default: () => {} },
        descricaoProduto: { type: String, default: '' },
        descricaoEtapa: { type: String, default: '' },
        idPronac: { type: String, default: '' },
        uf: { type: String, default: '' },
        produto: { type: Number, default: 0 },
        idmunicipio: { type: Number, default: 0 },
        etapa: { type: Number, default: 0 },
        cdProduto: { type: Number, default: 0 },
        cdUf: { type: Number, default: 0 },
    },
    data() {
        return {
            comprovantesIsLoading: false,
            loading: false,
            snackbarAlerta: false,
            snackbarTexto: '',
            itemEmAvaliacao: {},
            valid: true,
            rules: {
                required: v => !!v || 'Campo obrigatório',
                avaliacao: v => v !== '4' || 'Avaliação deve ser aprovado ou reprovado',
                parecer: v => (!!v || this.$refs.stItemAvaliado.value !== '3') || 'Parecer é obrigatório',
            },
        };
    },
    computed: {
        ...mapGetters({
            comprovantes: 'avaliacaoResultados/comprovantes',
            isModalVisible: 'modal/default',
        }),
        valorComprovacaoValidada() {
            if (Object.keys(this.comprovantes).length === 0) {
                return 0;
            }
            return this.comprovantes
                .map((item) => {
                    if (item.stItemAvaliado === '1') {
                        return item.valor;
                    }
                    return 0;
                }).reduce((total, valor) => total + valor);
        },
    },
    mounted() {
        if (this.isModalVisible === 'avaliacao-item') {
            this.atualizarComprovantes(true);
        }
    },
    methods: {
        ...mapActions({
            alterarPlanilha: 'avaliacaoResultados/alterarPlanilha',
            salvarAvaliacaoComprovante: 'avaliacaoResultados/salvarAvaliacaoComprovante',
            obterDadosItemComprovacao: 'avaliacaoResultados/obterDadosItemComprovacao',
            atualizarEstatisticasAvaliacaoAction: 'avaliacaoResultados/buscarEstatisticasAvaliacao',
            modalClose: 'modal/modalClose',
        }),
        getUrlParams() {
            return this.$route.params[0];
        },
        salvarAvaliacao(avaliacao) {
            if (!this.$refs.form.validate()) {
                return false;
            }
            const avaliando = Object.assign({}, avaliacao);
            this.loading = true;
            this.salvarAvaliacaoComprovante(avaliando).then((response) => {
                this.snackbarTexto = response.message;
                this.snackbarAlerta = true;
                this.loading = false;
            }).catch((e) => {
                this.snackbarTexto = e.message;
                this.snackbarAlerta = true;
                this.loading = false;
            });

            return true;
        },
        atualizarComprovantes(loading) {
            let params = '';
            if (typeof this.getUrlParams() !== 'undefined') {
                params = this.getUrlParams();
            } else {
                const idPronac = `idPronac/${this.idPronac}`;
                const uf = `uf/${this.uf}`;
                const produto = `produto/${this.produto}`;
                const idMunicipio = `idmunicipio/${this.idmunicipio}`;
                const idPlanilhaItem = `idPlanilhaItem/${this.item.idPlanilhaItens}`;
                const etapa = `etapa/${this.etapa}`;
                const idUf = `idUf/${this.cdUf}`;
                params = `${idPronac}/${idPlanilhaItem}/${produto}/${uf}/${idMunicipio}/${etapa}/${idUf}`;
            }

            if (loading) {
                this.comprovantesIsLoading = true;
                this.obterDadosItemComprovacao(params).catch().then(() => {
                    this.comprovantesIsLoading = false;
                });
            }
        },
        fecharModal() {
            this.modalClose();
            this.itemEmAvaliacao = {};
            this.alterarPlanilha({
                cdProduto: this.cdProduto,
                etapa: this.etapa,
                cdUf: this.cdUf,
                idmunicipio: this.idmunicipio,
                idPlanilhaItem: this.item.idPlanilhaItens,
            });
            this.atualizarEstatisticasAvaliacaoAction(this.idPronac);
        },
    },
};
</script>
