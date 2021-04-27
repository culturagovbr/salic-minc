<template>
    <div
        v-if="planilha.vlSolicitadoTotal
            || planilha.vlAprovadoTotal
        || planilha.vlSugeridoTotal"
        class="card-action planilha-consolidacao"
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
    mixins: [planilhas],
    props: {
        planilha: {
            type: [Array, Object],
            default: () => [],
        },
    },
};
</script>


<style>
</style>
