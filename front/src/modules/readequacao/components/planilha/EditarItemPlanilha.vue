<template>
    <v-container grid-list-md>
        <v-flex
            v-if="gravando"
            xs12
            text-xs-center
        >
            <carregando
                :text="'Gravando alterações no item...'"
            />
        </v-flex>
        <v-form
            ref="form"
        >
            <v-layout
                v-if="!gravando"
                row
                wrap
            >
                <v-flex
                    xs12
                    md3
                >
                    <v-select
                        v-model="itemEditado.idUnidade"
                        :value="itemEditado.idUnidade"
                        :items="getUnidadesPlanilha"
                        item-text="Descricao"
                        item-value="idUnidade"
                        label="Unidade"
                    />
                </v-flex>
                <v-flex
                    xs12
                    md1
                >
                    <v-text-field
                        v-model="itemEditado.QtdeDias"
                        :value="item.QtdeDias"
                        :rules="[rules.required]"
                        label="Qtd dias"
                        @change="atualizarCampo(removeLetras($event), 'QtdeDias')"
                    />
                </v-flex>
                <v-flex
                    xs12
                    md1
                >
                    <v-text-field
                        v-model="itemEditado.Quantidade"
                        :value="item.Quantidade"
                        :rules="[rules.required]"
                        label="Quantidade"
                        @change="atualizarCampo(removeLetras($event), 'Quantidade')"
                    />
                </v-flex>
                <v-flex
                    xs12
                    md1
                >
                    <v-text-field
                        v-model="itemEditado.Ocorrencia"
                        :value="item.Ocorrencia"
                        :rules="[rules.required]"
                        required
                        label="Ocorrência"
                        @change="atualizarCampo(removeLetras($event), 'Ocorrencia')"
                    />
                </v-flex>
                <v-flex
                    xs12
                    md2
                >
                    <label class="grey--text text--darken-1 caption">Valor unitário</label>
                    <div class="d-inline-block subheading">
                        R$
                        <input-money
                            ref="ValorUnitario"
                            :value="item.vlUnitario"
                            :rules="[rules.required, rules.nonZero]"
                            class="subheading"
                            @ev="atualizarCampo($event, 'ValorUnitario')"
                        />
                    </div>
                </v-flex>
                <v-flex
                    xs12
                    md2
                >
                    <label class="grey--text text--darken-1 caption">Valor total</label>
                    <div class="d-inline-block subheading">
                        R$
                        {{ valorTotal | filtroFormatarParaReal }}
                    </div>
                </v-flex>
                <v-flex
                    xs12
                    md12>
                    <v-textarea
                        ref="justificativa"
                        v-model="itemEditado.dsJustificativa"
                        :placeholder="'Justificativa do item'"
                        :min-char="minChar.justificativa"
                        :rules="[rules.required, rules.justificativaMin]"
                        label="Justificativa"
                        class="regular"
                        @change="atualizarCampo($event, 'dsJustificativa')"
                    />
                </v-flex>
                <v-flex
                    xs12
                    text-xs-left
                >
                    <v-btn
                        color="green lighten-1"
                        dark
                        @click="salvarItem()"
                    >Salvar item
                        <v-icon
                            right
                            dark
                        >done</v-icon>
                    </v-btn>
                    <v-btn
                        v-if="isAlterado()"
                        color="blue lighten-1"
                        dark
                        @click="reverterItem()"
                    >
                        Reverter
                        <v-icon
                            right
                            dark
                        >
                            restore
                        </v-icon>
                    </v-btn>
                    <v-btn
                        color="red lighten-1"
                        dark
                        @click="cancelarEdicao()"
                    >Cancelar
                        <v-icon
                            right
                            dark
                        >cancel</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-form>
    </v-container>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import { utils } from '@/mixins/utils';
import InputMoney from '@/components/InputMoney';
import Carregando from '@/components/CarregandoVuetify';
import MxReadequacao from '../../mixins/Readequacao';
import MxPlanilhaReadequacao from '../../mixins/PlanilhaReadequacao';
import SalicInputValor from '@/components/SalicInputValor';

export default {
    name: 'EditarItemPlanilha',
    components: {
        SalicInputValor,
        Carregando,
        InputMoney,
    },
    mixins: [
        utils,
        MxPlanilhaReadequacao,
        MxReadequacao,
    ],
    props: {
        item: {
            type: [Array, Object],
            default: () => {},
        },
    },
    data() {
        return {
            itemEditado: {
                dsJustificativa: '',
                Ocorrencia: 0,
                QtdeDias: 0,
                Quantidade: 0,
                ValorUnitario: '',
                idFonte: 0,
                idPlanilhaAprovacao: 0,
                idPlanilhaItem: 0,
                idReadequacao: 0,
                idPronac: 0,
                idProduto: 0,
                idUnidade: 0,
                idTipoReadequacao: 0,
            },
            campos: [
                'idUnidade',
                'QtdeDias',
                'Quantidade',
                'Ocorrencia',
                'dsJustificativa',
            ],
            minChar: {
                justificativa: 10,
            },
            rules: {
                required: v => !!v || 'Campo obrigatório.',
                nonZero: v => (v && v >= 0) || 'Campo não pode ser zerado ou negativo.',
                justificativaMin: v => (v && v.length >= this.minChar.justificativa) || this.justificativaMinMessage,
            },
            gravando: false,
        };
    },
    computed: {
        ...mapGetters({
            getUnidadesPlanilha: 'readequacao/getUnidadesPlanilha',
            getReadequacao: 'readequacao/getReadequacao',
        }),
        justificativaMinMessage() {
            return `Justificativa ter no mínimo ${this.minChar.justificativa} caracteres.`;
        },
        valorTotal() {
            return this.itemEditado.Ocorrencia * this.itemEditado.Quantidade * this.itemEditado.ValorUnitario;
        },
    },
    watch: {
        item() {
            this.inicializarItemEditado();
        },
    },
    created() {
        this.inicializarItemEditado();
    },
    methods: {
        ...mapActions({
            atualizarItemPlanilha: 'readequacao/atualizarItemPlanilha',
            reverterAlteracaoItem: 'readequacao/reverterAlteracaoItem',
        }),
        inicializarItemEditado() {
            this.itemEditado = {
                idPlanilhaAprovacao: this.item.idPlanilhaAprovacao,
                idPlanilhaItem: this.item.idPlanilhaItem,
                idReadequacao: this.getReadequacao.idReadequacao,
                idPronac: this.getReadequacao.idPronac,
                dsJustificativa: this.item.dsJustificativa,
                idUnidade: this.item.idUnidade,
                idFonte: this.item.idFonte,
                Ocorrencia: this.item.Ocorrencia,
                Quantidade: this.item.Quantidade,
                QtdeDias: this.item.QtdeDias,
                ValorUnitario: this.item.vlUnitario,
                idTipoReadequacao: this.getReadequacao.idTipoReadequacao,
            };
        },
        salvarItem() {
            if (this.itemEditado.dsJustificativa.length < this.minChar.justificativa) {
                this.$refs.form.validate();
                return;
            }
            this.gravando = true;
            this.atualizarItemPlanilha(this.itemEditado)
                .then(() => {
                    this.gravando = false;
                    this.$emit('fechar-item');
                });
        },
        cancelarEdicao() {
            this.$emit('fechar-item');
        },
        reverterItem() {
            this.reverterAlteracaoItem({
                idPronac: this.item.idPronac,
                idReadequacao: this.getReadequacao.idReadequacao,
                idTipoReadequacao: this.getReadequacao.idTipoReadequacao,
                idPlanilhaItem: this.item.idPlanilhaItem,
                idPlanilhaAprovacao: this.item.idPlanilhaAprovacao,
                idPlanilhaAprovacaoPai: this.item.idPlanilhaAprovacaoPai,
            });
        },
        atualizarCampo(valor, campo) {
            this.itemEditado[campo] = valor;
        },
        isAlterado() {
            const planilhaEdicao = [
                this.item.idUnidade,
                this.item.Ocorrencia,
                this.item.Quantidade,
                this.item.QtdeDias,
                this.item.vlUnitario,
            ];
            const planilhaAtiva = [
                this.item.idUnidadeAtivo,
                this.item.OcorrenciaAtivo,
                this.item.QuantidadeAtivo,
                this.item.QtdeDiasAtivo,
                this.item.vlUnitarioAtivo,
            ];
            return JSON.stringify(planilhaEdicao) !== JSON.stringify(planilhaAtiva);
        },
    },
};
</script>
