<template>
    <div
        class="card-action planilha-consolidacao"
        v-if="planilha.vlSolicitadoTotal
        || planilha.vlAprovadoTotal
        ||  planilha.vlSugeridoTotal"
    >
        <div class="totais">
            <div v-if="planilha.vlSolicitadoTotal">
                <b>Valor Solicitado Total</b>:
                R$ {{ planilha.vlSolicitadoTotal | filtroFormatarParaReal }}
            </div>
            <div v-if="planilha.vlSugeridoTotal">
                <b>Valor Sugerido Total</b>:
                R$ {{ planilha.vlSugeridoTotal | filtroFormatarParaReal }}
            </div>
            <div v-if="planilha.vlAprovadoTotal">
                <b>Valor Aprovado Total</b>:
                R$ {{ planilha.vlAprovadoTotal | filtroFormatarParaReal }}
            </div>
            <div v-if="planilha.vlComprovadoTotal">
                <b>Valor Comprovado Total</b>:
                R$ {{ planilha.vlComprovadoTotal | filtroFormatarParaReal }}
            </div>
        </div>
    </div>
</template>

<script>
    import planilhas from '@/mixins/planilhas';

    export default {
        props: {
            planilha: {},
        },
        mixins: [planilhas],
    };
</script>


<style>
</style>
