<template>
    <s-carregando
        v-if="loading"
        text="Carregando consolidacao"
    />
    <div v-else>
        <v-form
            ref="form"
            v-model="valid"
            lazy-validation
        >
            <v-layout wrap>
                <div
                    v-if="editorParecerRules.show"
                    class="text-xs-left"
                >
                    <p
                        :class="editorParecerRules.color"
                    >
                        {{ editorParecerRules.msg }}
                    </p>
                </div>
                <v-flex
                    xs12
                    sm12
                    md12
                >
                    <v-switch
                        v-model="consolidacaoEmEdicao.ParecerFavoravel"
                        :label="`Parecer Favor&aacute;vel?: ${labelSimOuNao(consolidacaoEmEdicao.ParecerFavoravel)}`"
                        color="green"
                        false-value="1"
                        true-value="2"
                    />
                </v-flex>
                <v-flex
                    xs12
                    sm12
                    md12
                    class="mb-2"
                >
                    <b>Valor sugerido:</b>
                    <span> R$ {{ consolidacaoEmEdicao.SugeridoReal | filtroFormatarParaReal }}</span>
                    <v-icon
                        class="ml-2"
                        @click="$root.$dialogAjuda(textoAjudaValorSugerido)"
                    >
                        help
                    </v-icon>
                </v-flex>
                <v-flex
                    xs12
                    sm12
                    md12
                >
                    <b>Parecer t&eacute;cnico</b>
                    <s-editor-texto
                        v-model="consolidacaoEmEdicao.ResumoParecer"
                        :placeholder="'Parecer t&eacute;cnico sobre o conte&uacute;do do produto'"
                        :min-char="minChar"
                        @editor-texto-counter="validateText($event)"
                    />
                </v-flex>
            </v-layout>
            <v-subheader class="pa-0">
                Todos os campos s&atilde;o obrigat&oacute;rios
            </v-subheader>

            <v-layout
                row
                justify-center
            >
                <v-btn
                    :loading="loadingButton"
                    :disabled="!valid || !textIsValid"
                    color="primary"
                    @click="submit"
                >
                    <v-icon left>
                        save
                    </v-icon>
                    Salvar
                </v-btn>
            </v-layout>
        </v-form>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import SEditorTexto from '@/components/SalicEditorTexto';
import SCarregando from '@/components/CarregandoVuetify';
import MxPlanilha from '@/mixins/planilhas';


export default {
    name: 'Consolidacao',
    components: { SEditorTexto, SCarregando },
    mixins: [MxPlanilha],
    props: {
        active: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            loadingButton: false,
            loading: true,
            valid: false,
            minChar: 10,
            textIsValid: false,
            editorParecerRules: {
                show: false,
                color: '',
                backgroundColor: '',
                msg: '',
                enable: false,
            },
            consolidacaoEmEdicao: {
                IdPRONAC: this.$route.params.idPronac,
                idProduto: this.$route.params.id,
                ParecerFavoravel: '2',
                ResumoParecer: '',
                SugeridoReal: 1000,
            },
            rules: {
                parecer: v => (!!v || this.$refs.stItemAvaliado.value !== '3') || 'Parecer &eacute; obrigat&oacute;rio',
            },
            textoAjudaValorSugerido: 'Valor sugerido de todos os produtos(prim&aacute;rios e secund&aacute;rios) do projeto',
        };
    },
    computed: {
        ...mapGetters({
            produto: 'parecer/getProduto',
            consolidacao: 'parecer/getConsolidacao',
        }),
    },
    watch: {
        consolidacao(val) {
            if (Object.keys(val).length > 0) {
                this.consolidacaoEmEdicao = Object.assign({}, this.consolidacaoEmEdicao, val);
            }
            this.loading = false;
        },
    },
    created() {
        this.obterConsolidacao({
            id: this.$route.params.id,
            idPronac: this.$route.params.idPronac,
        });
    },
    methods: {
        ...mapActions({
            obterConsolidacao: 'parecer/obterConsolidacao',
            salvarAnaliseConsolidacao: 'parecer/salvarAnaliseConsolidacao',
        }),
        labelSimOuNao(val) {
            return val === '2' ? 'Sim' : 'Não';
        },
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
        submit() {
            // if (!this.$refs.form.validate()) {
            //     return false;
            // }

            const analise = Object.assign({}, this.consolidacaoEmEdicao);

            this.loadingButton = true;
            this.salvarAnaliseConsolidacao(analise).then(() => {
                this.loadingButton = false;
            }).catch(() => {
                this.loadingButton = false;
            });

            return true;
        },
    },
};
</script>
