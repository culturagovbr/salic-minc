<template>
    <div>
        <planilha
            :array-planilha="proposta.tbplanilhaproposta"
        />
    </div>
</template>
<script>
import Planilha from '@/components/Planilha/Planilha';

export default {
    name: 'PropostaPlanilha',
    components: {
        Planilha,
    },
    props: {
        proposta: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
