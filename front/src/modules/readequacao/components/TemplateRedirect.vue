<template>
    <div/>
</template>
<script>
export default {
    name: 'TemplateRedirect',
    props: {
        campo: {
            type: Object,
            default: () => {},
        },
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        redirecionar: {
            type: Boolean,
            default: () => false,
        },
    },
    data() {
        return {
            tiposReadequacoesRedirect: {
                local_realizacao: '/readequacao/local-realizacao/index/?idPronac=',
                planilha: '/readequacao/readequacoes/planilha-orcamentaria/?idPronac=',
                saldo_aplicacao: '/readequacao/saldo-aplicacao/?idPronac=',
                plano_distribuicao: '/readequacao/plano-distribuicao/index/?idPronac=',
                remanejamento_50: '/readequacao/remanejamento-menor/index/?idPronac=',
                transferencia_recursos: '/readequacao/transferencia-recursos/index/?idPronac=',
            },
        };
    },
    computed: {
        urlRedirect() {
            const chave = `key_${this.dadosReadequacao.idTipoReadequacao}`;
            if (typeof this.campo !== 'undefined') {
                if (Object.prototype.hasOwnProperty.call(this.campo, chave)) {
                    if (typeof this.campo[chave].tpCampo !== 'undefined') {
                        return this.tiposReadequacoesRedirect[this.campo[chave].tpCampo];
                    }
                }
            }
            return '';
        },
    },
    watch: {
        redirecionar() {
            if (this.redirecionar) {
                this.executaRedirecionamento();
            }
        },
    },
    methods: {
        executaRedirecionamento() {
            if (this.urlRedirect !== 'undefined') {
                let routePath = this.urlRedirect + String(this.dadosReadequacao.idPronac);
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
