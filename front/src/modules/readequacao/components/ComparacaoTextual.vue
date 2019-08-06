<template>
    <campo-diff
        :original-text="original.valor"
        :changed-text="changed.dsSolicitacao"
        :method="'diffWordsWithSpace'"
    />
</template>
<script>
import CampoDiff from '@/components/CampoDiff';

export default {
    name: 'ComparacaoTextual',
    components: [
        CampoDiff,
    ],
    props: {
        original: {
            type: Object,
            default: () => {},
        },
        changed: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
