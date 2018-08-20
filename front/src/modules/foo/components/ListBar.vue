<template>
    <div>
        <h1>Componente Bar</h1>
        <CreateBar/>
        <table>
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>DadoNr</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(registro, index) in dadosTabela" :key="index">
                    <td>{{ registro.Codigo }}</td>
                    <td>{{ registro.DadoNr }}</td>
                    <td>
                        <div class="atualizar-action">
                            <UpdateBar :registroAtivo="registro"/>
                        </div>
                        <div class="remover-action">
                            <a
                                style="width: 150px"
                                class="btn btn-danger"
                                @click="confirmationRemove(registro)"
                            >
                                Remover
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import CreateBar from './CreateBar';
import UpdateBar from './UpdateBar';

export default {
    name: 'ListBar',
    created() {
        this.obterDadosTabela();
    },
    components: {
        CreateBar,
        UpdateBar,
    },
    computed: {
        ...mapGetters({
            dadosTabela: 'foo/dadosTabela',
        }),
    },
    methods: {
        ...mapActions({
            obterDadosTabela: 'foo/obterDadosTabela',
            setRegistroAtivo: 'foo/setRegistroAtivo',
            removerRegistro: 'foo/removerRegistro',
        }),
        confirmationRemove(registro) {
            const currentConfirm = confirm;
            const trueResponse = currentConfirm('Deseja removerr esse registro?');

            if (trueResponse) {
                this.removerRegistro(registro);
            }
        },
    },
};
</script>

<style>
.atualizar-action {
    display:inline-block;
    margin-right:1px;
    width: 150px;
}

.remover-action {
    display:inline-block;
    width: 150px;
}
</style>
