<template>
    <v-card tile>
        <v-card-text>
            <template
                v-if="loading"
            >
                <carregando
                    :text="'Montando comparação'"
                    class="mt-5 pb-4"
                />
            </template>
            <template v-else>
                <template
                    v-if="disponivelComparacao"
                >
                    <campo-diff
                        :original-text="getDadosCampo.valor"
                        :changed-text="dadosReadequacao.dsSolicitacao"
                        :method="'diffWordsWithSpace'"
                    />
                </template>
                <div v-else>
                    <div class="mb-3">
                        Este tipo de readequação ainda não possui uma visualização específica!
                    </div>
                    <v-btn
                        color="secondary"
                        @click.stop="redirect()"
                    >
                        <v-icon>
                            gavel
                        </v-icon>
                        Visualizar modo antigo
                    </v-btn>
                </div>
            </template>
        </v-card-text>
    </v-card>
</template>
<script>
import { mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import CampoDiff from '@/components/CampoDiff';
import MxReadequacao from '../mixins/Readequacao';

export default {
    name: 'AnalisarAlteracoes',
    components: {
        Carregando,
        CampoDiff,
    },
    mixins: [
        MxReadequacao,
    ],
    data() {
        return {
            loading: true,
            tiposComponentesRedirect: {
                local_realizacao: '/readequacao/readequacoes/form-avaliar-readequacao?id=',
                planilha: '/readequacao/readequacoes/form-avaliar-readequacao?id=',
                saldo_aplicacao: '/readequacao/readequacoes/form-avaliar-readequacao?id=',
                plano_distribuicao: '/readequacao/readequacoes/form-avaliar-readequacao?id=',
                transferencia_recursos: '/readequacoes/form-avaliar-readequacao?id=',
            },
        };
    },
    computed: {
        ...mapGetters({
            dadosReadequacao: 'readequacao/getReadequacao',
            campoAtual: 'readequacao/getCampoAtual',
        }),
        getDadosCampo() {
            return this.parseDadosCampo(this.campoAtual);
        },
        tipoComponente() {
            let tipoComponente = '';
            const chave = `key_${this.dadosReadequacao.idTipoReadequacao}`;
            if (Object.prototype.hasOwnProperty.call(this.campoAtual, chave)) {
                tipoComponente = this.campoAtual[chave].tpCampo;
            }
            return tipoComponente;
        },
        urlRedirect() {
            if (this.tipoComponente !== '') {
                return this.tiposComponentesRedirect[this.tipoComponente] + this.dadosReadequacao.idReadequacao;
            }
            return '';
        },
        disponivelComparacao() {
            return (this.getDadosCampo.valor !== ''
                    && typeof this.getDadosCampo.valor !== 'undefined'
                    && this.dadosReadequacao.dsSolicitacao !== ''
                    && typeof this.dadosReadequacao.dsSolicitacao !== 'undefined'
            );
        },
    },
    watch: {
        getDadosCampo: {
            handler(value) {
                if (value.tpCampo !== '') {
                    this.loading = false;
                }
            },
            deep: true,
        },
    },
    mounted() {
        if (this.getDadosCampo.tpCampo !== '') {
            this.loading = false;
        }
    },
    methods: {
        redirect() {
            if (this.tipoComponente !== '') {
                if (Object.prototype.hasOwnProperty.call(this.tiposComponentesRedirect, this.tipoComponente)) {
                    this.abreUrl();
                }
            }
        },
        abreUrl() {
            if (this.urlRedirect !== 'undefined') {
                let routePath = this.urlRedirect;
                if (routePath.match(/#/)) {
                    routePath = routePath.replace(/#/, '');
                    this.$router.push({ path: routePath });
                } else {
                    window.location.href = routePath;
                }
            }
        },
    },
};
</script>
