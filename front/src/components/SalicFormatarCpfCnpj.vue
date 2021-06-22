<template>
    <span
        v-if="cpfCnpjFormatado && cpfCnpjFormatado.length > 0"
        v-html="cpfCnpjFormatado"/>
    <span v-else>N&atilde;o informado(a)</span>
</template>

<script>
export default {
    name: 'SalicFormatarCpfCnpj',
    props: { cpf: { type: String, default: '' } },
    computed: {
        cpfCnpjFormatado() {
            if (typeof this.cpf !== 'undefined') {
                return this.formatarCpfOuCnpj(this.cpf);
            }

            return '';
        },
    },
    methods: {
        formatarCpfOuCnpj(cpfOrCnpj) {
            if (cpfOrCnpj.length === 11) {
                return this.formatarCpf(cpfOrCnpj);
            }
            return this.formatarCnpj(cpfOrCnpj);
        },
        // formato: 999.999.999-99
        formatarCpf(v) {
            let value = v.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1-$2');
            return value;
        },
        // formato: 99.999.999/9999-99
        formatarCnpj(v) {
            let value = v.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            return value;
        },
    },
};
</script>
