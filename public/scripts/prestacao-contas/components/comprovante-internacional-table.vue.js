Vue.component('sl-comprovante-internacional-table', {
    template: `
        <div>
            <table class="bordered">
                <tbody>
                    <tr>
                        <th>Fornecedor</th>
                        <td>{{dados.fornecedor.nome}}</td>
                        <th>Endere&ccedil;o</th>
                        <td colspan="5">{{dados.fornecedor.endereco}}</td>
                        <th>Pa&iacute;s</th>
                        <td colspan="5">{{dados.fornecedor.nacionalidade}}</td>
                    </tr>
                    <tr>
                        <th>Tipo Comprovante</th>
                        <td>{{tipoDocumento}}</td>
                        <th>N�mero</th>
                        <td>{{dados.numero}}</td>
                        <th>S&eacute;rie</th>
                        <td colspan="3">{{dados.serie}}</td>
                    </tr>
                    <tr>
                        <th>Dt. Emiss&atilde;o do comprovante de despesa</th>
                        <td>{{dataEmissaoComprovante}}</td>
                        <th>Data do Pagamento</th>
                        <td>{{dataPagamentoFormatado}}</td>
                    </tr>
                    <tr>
                        <th>Valor</th>
                        <td>R$  {{valorFormatado}}</td>
                        <th>Arquivo</th>
                        <td colspan="5">
                            <a :href="'/upload/abrir/id/' + dados.idArquivo">{{nomeArquivo}}</a>
                        </td>
                    </tr>
                    <tr>
                        <th>Justificativa do Proponente</th>
                        <td colspan="7">{{dados.justificativa}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    `,
    props: ['dados'],
    mounted: function () {
        var vue = this;
        var url = '/prestacao-contas/gerenciar/pais' ;

        $3.ajax({
            url: url,
            dataType: "json",
        }).done(function(data){
            vue.pais = data;
        });
    },
    data() {
        return {
            pais: [],
        }
    },
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
            return moment(this.dados.dataEmissao).format('DD/MM/Y');
        },
        dataPagamentoFormatado() {
            if (moment(this.dados.dataPagamento).isValid()){
                return moment(this.dados.dataPagamento).format('DD/MM/Y');
            } 
            return moment(this.dados.dataPagamento, 'DD/MM/Y').format('DD/MM/Y');
        },
        tipoDocumento() {
            tipo = '';
            switch(parseInt(this.dados.tipo)) {
                case 1: tipo = 'Cupom Fiscal'; break;
                case 2: tipo = 'Guia de Recolhimentol'; break;
                case 3: tipo = 'Nota Fiscal/Fatura'; break;
                case 4: tipo = 'Recibo de Pagamento'; break;
                case 5: tipo = 'RPA'; break;
                case 6: tipo = 'INVOICE'; break;
                case 7: tipo = 'OUTROS'; break;
            }

            return tipo;
        },
        formaPagamento() {
            forma = '';
            switch(parseInt(this.dados.forma)) {
                case 1: forma = 'Cheque'; break;
                case 2: forma = 'Transferência Bancária'; break;
                case 3: forma = 'Saque/Dinheiro'; break;
            }

            return forma;
        },
        valorFormatado() {
           return  numeral(parseFloat(this.dados.valor)).format('0,0.00');
        },
        nomeArquivo() {
           return  this.dados.arquivo.nome;
        },
    }
});
