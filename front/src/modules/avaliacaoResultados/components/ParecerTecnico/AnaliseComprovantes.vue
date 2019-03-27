<template>
    <div>
        AnaliseComprovantes
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ModalTemplate from '@/components/modal';

export default {
    name: 'Painel',
    components: {
        ModalTemplate,
    },
    data() {
        return {
            DadoNr: null,
        };
    },
    computed: mapGetters({
        modalVisible: 'modal/default',
    }),
    methods: {
        ...mapActions({
            criarRegistro: 'foo/criarRegistro',
            modalOpen: 'modal/modalOpen',
            modalClose: 'modal/modalClose',
        }),
        fecharModal() {
            // eslint-disable-next-line
            $3('#modalTemplate').modal('close');
            this.modalClose();
        },
    },
};
</script>
