export default {
    methods: {
        abrirArquivo(idDocumento) {
            const urlArquivo = `/readequacao/readequacoes/abrir-documento-readequacao?id=${idDocumento}`;
            window.location.href = urlArquivo;
        },
    },
};
