export const fnSetCookie = (cname, cvalue, exdays) => {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    const utCString = d.toUTCString();
    const expires = `expires=${utCString}`;
    document.cookie = `${cname}=${cvalue};${expires};path=/`;
};

export const fnGetCookie = (cname) => {
    const name = cname.concat('=');
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i += 1) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
};
