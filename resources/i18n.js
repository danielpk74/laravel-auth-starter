import { createI18n } from "laravel-vue-i18n";
import en from './locales/en.json';
import es from './locales/es.json';


function loadLocalMessages() {
    const locales = [{ en: en }, {es: es}];
    const messages = {};

    locales.forEach(lang => {
        const key = Object.keys(lang);
        messages[key] = lang[key];
    });

    return messages;
}


export default loadLocalMessages;
