import Vue from 'vue';
import Vuetify from 'vuetify/lib';
import pl from 'vuetify/src/locale/pl.ts';
import en from 'vuetify/src/locale/en.ts';
import { getSuggestedLanguage } from '../i18n/i18n';

Vue.use(Vuetify);

const opts = {
    theme: {
        themes: {
            light: {
                primary: '#1071ff',
                secondary: '#FFCDD2',
                accent: '#0e489d',
            },
        },
    },
    options: {
        themeVariations: ['primary', 'secondary', 'accent'],
        extra: {
            mainToolbar: {
                color: 'primary',
            },
            sideToolbar: {
            },
            sideNav: 'primary',
            mainNav: 'primary lighten-1',
            bodyBg: '',
        },
    },
    lang: {
        locales: { pl, en },
        current: getSuggestedLanguage(),
    },
};

export default new Vuetify(opts);
