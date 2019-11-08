<template>
    <div
        v-if="texto && texto.length > 3"
        v-html="texto"/>
    <div v-else>N&atilde;o informado(a)</div>
</template>

<script>
export default {
    name: 'SalicTextoSimples',
    props: {
        texto: { type: String, default: '' },
    },
};
</script>
