<template>
    <v-form
        ref="form"
        v-model="valid"
        lazy-validation
    >
        <v-layout
            row
            wrap
        >
            <v-flex xs12>
                <b>Avaliação</b>
                <v-radio-group
                    ref="stItemAvaliado"
                    v-model="itemEmAvaliacao.stItemAvaliado"
                    :rules="[rules.required, rules.avaliacao]"
                    name="stItemAvaliado"
                    type="radio"
                    validate-on-blur
                    row
                >
                    <v-radio
                        label="Aprovado"
                        value="1"
                        name="stItemAvaliadoModel"
                        color="green"
                    />
                    <v-radio
                        label="Reprovado"
                        value="3"
                        name="stItemAvaliadoModel"
                        color="red"
                    />
                </v-radio-group>
            </v-flex>
            <v-flex xs12>
                <v-textarea
                    v-model="itemEmAvaliacao.dsOcorrenciaDoTecnico"
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
            pa-0
        >
            <v-btn
                :disabled="!valid"
                :loading="loading"
                @click="salvarAvaliacao()"
            >
                <v-icon
                    left
                    dark
                >
                    save
                </v-icon>
                Salvar
            </v-btn>
        </v-container>
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
    </v-form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import MxPlanilha from '@/mixins/planilhas';

export default {
    name: 'AnalisarItemFormulario',
    mixins: [MxPlanilha],
    props: {
        comprovante: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
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
        this.itemEmAvaliacao = Object.assign({}, this.comprovante);
    },

    methods: {
        ...mapActions({
            salvarAvaliacaoComprovanteAction: 'avaliacaoResultados/salvarAvaliacaoComprovante',
        }),
        salvarAvaliacao() {
            if (!this.$refs.form.validate()) {
                return false;
            }
            this.loading = true;
            this.salvarAvaliacaoComprovanteAction(this.itemEmAvaliacao)
                .then((response) => {
                    this.snackbarTexto = response.message;
                }).catch((e) => {
                    this.snackbarTexto = e.message;
                }).finally(() => {
                    this.snackbarAlerta = true;
                    this.loading = false;
                });

            return true;
        },
    },
};
</script>
