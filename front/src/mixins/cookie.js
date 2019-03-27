import { fnSetCookie, fnGetCookie } from './funcoes/cookie';

export default {
    methods: {
        setCookie(cname, cvalue, exdays) {
            return fnSetCookie(cname, cvalue, exdays);
        },
        getCookie(cname) {
            return fnGetCookie(cname);
        },
    },
};
