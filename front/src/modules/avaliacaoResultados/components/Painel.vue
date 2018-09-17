<template>
    <v-container fluid>
       <TabelaProjetos
               :analisar="true"
       ></TabelaProjetos>
    </v-container>
</template>

<script>
import TabelaProjetos from './TabelaProjetos';

export default {
    name: 'Painel',
    data() {
        return {
        };
    },
    components: {
        TabelaProjetos,
    },
};
</script>
