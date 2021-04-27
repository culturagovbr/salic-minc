export default (value) => {
    const moeda = Number(value);
    return moeda.toLocaleString('pt-br', { style: 'decimal', maximumFractionDigits: 2, minimumFractionDigits: 2 });
};
