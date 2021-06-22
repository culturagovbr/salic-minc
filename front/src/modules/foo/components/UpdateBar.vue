<template>
    <div>
        <a
            style="width: 150px;"
            class="btn btn-primary"
            @click="modalOpen('atualizar-bar');
                    setRegistroAtivo(registroAtivo);"
        >
            Atualizar
        </a>
        <ModalTemplate
            v-if="modalVisible === 'atualizar-bar'"
            @close="fecharModal()">
            <template slot="header">Atualizar Bar</template>
            <template slot="body">
                <form action="">
                    <label for="registro">DadoNr</label>
                    <input
                        :value="registro.DadoNr"
                        type="text"
                        name="DadoNr"
                        @input="buildRegistro">
                </form>
            </template>
            <template slot="footer">
                <a
                    class="btn btn-danger"
                    @click="fecharModal();$event.preventDefault()">Fechar</a>
                <a
                    class="btn btn-primary"
                    @click="checkChangesAndUpdate();fecharModal();">Atualizar</a>
            </template>
        </ModalTemplate>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ModalTemplate from '@/components/modal';

export default {
    name: 'UpdateBar',
    components: {
        ModalTemplate,
    },
    props: {
        registroAtivo: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            currentRegistro: {
                Codigo: '',
                DadoNr: '',
            },
        };
    },
    computed: {
        ...mapGetters({
            registro: 'foo/registro',
            modalVisible: 'modal/default',
        }),
    },
    methods: {
        ...mapActions({
            atualizarRegistro: 'foo/atualizarRegistro',
            setRegistroAtivo: 'foo/setRegistroAtivo',
            modalOpen: 'modal/modalOpen',
            modalClose: 'modal/modalClose',
        }),
        buildRegistro(event) {
            const DadoNr = event.target.value;
            this.currentRegistro.DadoNr = DadoNr;
            this.currentRegistro.Codigo = this.registro.Codigo;
        },
        checkChangesAndUpdate() {
            if (this.currentRegistro !== this.registro) {
                this.atualizarRegistro(this.currentRegistro);
            }
        },
        fecharModal() {
            // eslint-disable-next-line
            $3('#modalTemplate').modal('close');
            this.modalClose();
        },
    },
};
</script>
