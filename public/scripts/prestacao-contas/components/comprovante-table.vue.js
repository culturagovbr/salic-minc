const comprovanteTable = {
    props: [
        'dados',
    ],
    template: `
        <div>
            <table class="bordered">
                <tbody>
                    <tr>
                        <th>Fornecedor</th>
                        <td>{{dados.Descricao}}</td>
                        <th>CNPJ/CPF</th>
                        <td colspan="5">{{CNPJCPF}}</td>
                    </tr>
                    <tr>
                        <th>Comprovante</th>
                        <td>{{dados.tpDocumento}}</td>
                        <th>Número</th>
                        <td>{{dados.nrComprovante}}</td>
                        <th>S&eacute;rie</th>
                        <td colspan="3">{{dados.nrSerie}}</td>
                    </tr>
                    <tr>
                        <th>Dt. Emiss&atilde;o do comprovante de despesa</th>
                        <td>{{dataEmissaoComprovante}}</td>
                        <th>Forma de Pagamento</th>
                        <td>{{dados.tpFormaDePagamento}}</td>
                        <th>Data do Pagamento</th>
                        <td>{{dataPagamento}}</td>
                        <th>N&ordm; Documento Pagamento</th>
                        <td>{{dados.nrDocumentoDePagamento}}</td>
                    </tr>
                    <tr>
                        <th>Valor</th>
                        <td>R$  {{valor}}</td>
                        <th>Arquivo</th>
                        <td colspan="5">
                            <a :href="'/upload/abrir/id/' + dados.idArquivo">{{dados.nmArquivo}}</a>
                        </td>
                    </tr>
                    <tr>
                        <th>Justificativa do Proponente</th>
                        <td colspan="7">{{dados.dsJustificativa}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    `,
    computed: {
        CNPJCPF() {
            CNPJCPF = null;

            if (this.dados.fornecedor.CNPJCPF.length > 11) {
                CNPJCPF = this.dados.fornecedor.CNPJCPF.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/,'$1.$2.$3/$4-$5');
            } else {
                CNPJCPF = this.dados.fornecedor.CNPJCPF.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})/,'$1.$2.$3-$4');
            }

            return CNPJCPF;
        },
        dataEmissaoComprovante() {
            return moment(this.dados.dtEmissao).format('DD/MM/Y');
        },
        dataPagamento() {
            return moment(this.dados.dtPagamento).format('DD/MM/Y');
        },
        valor() {
            return parseFloat(this.dados.vlComprovacao).toFixed(2);
        }
    }
}
