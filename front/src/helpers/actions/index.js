export default (response) => {
    const { data } = response;
    const currentData = data.data;
    const { items } = currentData;

    return items;
};
