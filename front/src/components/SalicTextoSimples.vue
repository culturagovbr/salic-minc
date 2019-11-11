<template>
    <div
        v-if="texto && stripTags(texto).length > 0"
        v-html="texto"/>
    <div v-else>N&atilde;o informado(a)</div>
</template>

<script>
export default {
    name: 'SalicTextoSimples',
    props: {
        texto: { type: String, default: '' },
    },
    methods: {
        stripTags(string) {
            if (typeof string !== 'string') {
                return string;
            }
            return string.replace(/(<([^>]+)>)/ig, '');
        },
    },
};
</script>
