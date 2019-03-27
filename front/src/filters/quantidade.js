export default (value) => {
    const quantidade = Number(value);
    return quantidade.toLocaleString('pt-br', { style: 'decimal' });
};
