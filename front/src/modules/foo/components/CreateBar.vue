<template>
    <div>
        <a
            class="btn btn-primary"
            @click="modalOpen('create-bar'); inputClear();">
            Criar
        </a>
        <ModalTemplate
            v-if="modalVisible === 'create-bar'"
            @close="fecharModal()">
            <template slot="header">Criar Bar</template>
            <template slot="body">
                <form action="">
                    <p>
                        <label for="DadoNr">DadoNr</label>
                        <input
                            id="DadoNr"
                            v-model="DadoNr"
                            type="text"
                            name="DadoNr">
                    </p>
                </form>
            </template>
            <template slot="footer">
                <a
                    class="btn btn-danger"
                    @click="fecharModal();$event.preventDefault()">
                    Fechar
                </a>
                <a
                    class="btn btn-primary"
                    @click="criarRegistro({ DadoNr });fecharModal();">Salvar</a>
            </template>
        </ModalTemplate>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ModalTemplate from '@/components/modal';

export default {
    name: 'CreateBar',
    components: {
        ModalTemplate,
    },
    data() {
        return {
            DadoNr: '',
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
        inputClear() {
            this.DadoNr = '';
        },
    },
};
</script>
