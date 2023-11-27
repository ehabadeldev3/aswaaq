import { createI18n } from 'vue-i18n';
// import en from "../locales/admin/en.json";
import ar from "../locales/admin/ar.json";
import en from "../locales/admin/en.json";


const i18n = createI18n({
    legacy: false,
    locale: localStorage.getItem('langAdmin') ?? 'ar', // set locale
    fallbackLocale: 'en', // set fallback locale
    messages: {
        ar,en
    }
});


export default i18n;
