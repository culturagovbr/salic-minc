<template>
    <div>
        <v-card>
            <v-card-text>
                <salic-texto-simples :texto="texto" />
            </v-card-text>
        </v-card>
    </div>
</template>
<script>

import SalicTextoSimples from '@/components/SalicTextoSimples';

export default {
    name: 'PropostaTexto',
    components: {
        SalicTextoSimples,
    },
    props: {
        proposta: {
            type: Object,
            default: () => {},
        },
        value: {
            type: String,
            default: '',
        },
    },
    computed: {
        texto() {
            return this.proposta[this.value];
        },
    },
};
</script>
