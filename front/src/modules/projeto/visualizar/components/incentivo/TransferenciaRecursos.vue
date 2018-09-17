<template>
    <div>
        <div :class="tipoAcao">
            <a
                class="cursor"
                @click="abrirModal('transferencia-recursos');"
            >
                {{valor | formatarParaReal}}
            </a>
        </div>
        <ModalTemplate v-if="modalVisible === 'transferencia-recursos'" @close="fecharModal();">
            <template slot="header">
                <div style="float: left; margin-bottom: 20px;">
                    Transfer&ecirc;ncia de recursos entre projetos culturais
                </div>
            </template>
            <template class="striped" slot="body">
                <table>
                    <thead>
                        <tr>
                            <th style="background-color: #ff0000; color: #ffffff;" colspan="2">Projeto Transferidor</th>
                            <th style="background-color: #00b0f0; color: #ffffff;" colspan="4">Projeto Recebedor</th>
                        </tr>
                        <tr>
                            <th>Pronac</th>
                            <th>Nome do Projeto</th>
                            <th>Pronac</th>
                            <th>Nome do Projeto</th>
                            <th>Dt. Recebimento</th>
                            <th style="text-align: right;">Vl. Recebido</th>
                        </tr>
                    </thead>
                    <tbody v-for="(informacoesTransferencia, index) in transferenciaRecursos" :key="index">
                        <tr>
                            <td>{{informacoesTransferencia.PronacTransferidor}}</td>
                            <td>{{informacoesTransferencia.NomeProjetoTranferidor}}</td>
                            <td>{{informacoesTransferencia.PronacRecebedor}}</td>
                            <td>{{informacoesTransferencia.NomeProjetoRecedor}}</td>
                            <td>{{informacoesTransferencia.dtRecebimento}}</td>
                            <td>R${{informacoesTransferencia.vlRecebido | formatarParaReal}}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="1">Total</td>
                            <td style="text-align: right" colspan="5">R${{ somaValoresRecebidos | formatarParaReal }}</td>
                        </tr>
                    </tfoot>
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
        data() {
            return {
                somaValoresRecebidos: 0,
            };
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
            tipoAcao() {
                let cssClass = '';

                switch (this.acao) {
                case 'transferidor':
                    cssClass = 'destaque-texto-secondary';
                    break;
                case 'recebedor':
                    cssClass = 'destaque-texto-primary';
                    break;
                default:
                    throw new Error('acao invalida');
                }

                return cssClass;
            },
        },
        watch: {
            transferenciaRecursos(queryResult) {
                let somaValesRecebido = 0;

                Object.entries(queryResult).forEach((value) => {
                    somaValesRecebido += parseFloat(value[1].vlRecebido, 10);
                });

                this.somaValoresRecebidos = somaValesRecebido;
            },
        },
    };
</script>
