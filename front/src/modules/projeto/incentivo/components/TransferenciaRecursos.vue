<template>
    <div>
        <a
            class="cursor"
            @click="abrirModal('transferencia-recursos');"
        >
            {{valor | formatarParaReal}}
        </a>
        <ModalTemplate v-if="modalVisible === 'transferencia-recursos'" @close="fecharModal();">
            <template slot="header">
                <div style="float: left; margin-bottom: 20px;">
                    Transferencia de recursos entre projetos culturais
                </div>
            </template>
            <template class="striped" slot="body">
                <table>
                    <thead>
                        <tr>
                            <th colspan="2">Projeto Recebedor</th>
                            <th colspan="4">Projeto Transferidor</th>
                        </tr>
                        <tr>
                            <th>Pronac</th>
                            <th>Nome do Projeto</th>
                            <th>Pronac</th>
                            <th>Nome do Projeto</th>
                            <th>Dt. Recebimento</th>
                            <th>Vl. Recebido</th>
                        </tr>
                    </thead>
                    <tbody v-for="(informacoesTransferencia, index) in transferenciaRecursos" :key="index">
                        <tr>
                            <td>{{informacoesTransferencia.idPronacTransferidor}}</td>
                            <td>{{informacoesTransferencia.NomeProjetoTranferidor}}</td>
                            <td>{{informacoesTransferencia.idPronacRecebedor}}</td>
                            <td>{{informacoesTransferencia.NomeProjetoRecedor}}</td>
                            <td>{{informacoesTransferencia.dtRecebimento | formatarData}}</td>
                            <td>{{informacoesTransferencia.vlRecebido | formatarParaReal}}</td>
                        </tr>
                    </tbody>
                </table>
            </template>
            <template slot="footer"></template>
        </ModalTemplate>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import ModalTemplate from '@/components/modal';
    import planilhas from '@/mixins/planilhas';

    export default {
        name: 'ValorTransferido',
        props: {
            valor: {
                String,
                required: true,
            },
            acao: {
                String,
                required: true,
            },
        },
        components: {
            ModalTemplate,
        },
        mixins: [planilhas],
        methods: {
            ...mapActions({
                modalOpen: 'modal/modalOpen',
                modalClose: 'modal/modalClose',
                buscarTransferenciaRecursos: 'projeto/buscarTransferenciaRecursos',
            }),
            abrirModal(modalName) {
                this.buscarTransferenciaRecursos(this.acao);
                // eslint-disable-next-line
                $3('#modalTemplate').modal('open');
                this.modalOpen(modalName);
            },
            fecharModal() {
                // eslint-disable-next-line
                $3('#modalTemplate').modal('close');
                this.modalClose();
            },
        },
        computed: {
            ...mapGetters({
                modalVisible: 'modal/default',
                transferenciaRecursos: 'projeto/transferenciaRecursos',
            }),
        },
    };
</script>

<style>
    .cursor {
        cursor: pointer;
    }
</style>
