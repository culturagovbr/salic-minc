export default (value) => {
    let data = '';
    if (value) {
        data = value.trim();
    }
    const currentValue = data;

    if (currentValue.length > 11) {
        return currentValue.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }

    return currentValue.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
};
