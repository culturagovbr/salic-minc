<template>
    <div
        v-if="cepFormatado && cepFormatado.length > 0"
        v-html="cepFormatado"/>
    <div v-else>N&atilde;o informado(a)</div>
</template>

<script>
export default {
    name: 'SalicFormatarCep',
    props: {

        cep: {
            type: String,
            default: String,
        },
    },
    computed: {
        cepFormatado() {
            if (typeof this.cep !== 'undefined') {
                return this.formatarCep(this.cep);
            }
            return '';
        },
    },
    methods: {
        formatarCep(cep) {
            if (cep.length === 8) {
                return this.adicionarMascaraCep(cep);
            }

            return '';
        },
        // formato: 99.999.999
        adicionarMascaraCep(v) {
            let value = v.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1-$2');
            return value;
        },
    },
};
</script>
