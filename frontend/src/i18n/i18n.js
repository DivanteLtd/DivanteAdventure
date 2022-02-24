import Vue from 'vue';
import VueI18n from 'vue-i18n';
import pl from './pl';
import en from './en';
import moment from '@divante-adventure/work-moment';

const messages = {
  pl,
  en,
};

export const languages = [
  {
    text: 'English / Angielski',
    value: 'en',
  }, {
    text: 'Polish / Polski',
    value: 'pl',
  },
];

export const supportedLanguages = [
  {
    code: 'pl',
    label: {
      pl: 'Polski',
      en: 'Polish',
    },
  }, {
    code: 'en',
    label: {
      pl: 'Angielski',
      en: 'English',
    },
  },
];

const splittedMonths = {
  pl: [
    'Sty.czeń',
    'Lu.ty',
    'Ma.rzec',
    'Kwie.cień',
    'Maj',
    'Czer.wiec',
    'Li.piec',
    'Sier.pień',
    'Wrze.sień',
    'Paź.dzier.nik',
    'Lis.to.pad',
    'Gru.dzień',
  ],
  en: [
    'Ja.nu.a.ry',
    'Feb.ru.a.ry',
    'March',
    'Ap.ril',
    'May',
    'June',
    'Ju.ly',
    'Au.gust',
    'Sep.tem.ber',
    'Oc.to.ber',
    'No.vem.ber',
    'De.cem.ber',
  ],
};


const fallbackLanguage = 'en';

export function getSuggestedLanguage() {
  return localStorage.getItem('chosenLanguage') || fallbackLanguage;
}

export function setLanguage(languageCode) {
  const supportedCodes = supportedLanguages.map(lang => lang.code);
  if (supportedCodes.includes(languageCode)) {
    localStorage.setItem('chosenLanguage', languageCode);
    window.location.reload();
  }
}

export function isSupportedLanguage(languageCode) {
  const supportedCodes = supportedLanguages.map(lang => lang.code);
  return supportedCodes.includes(languageCode);
}


Vue.use(VueI18n);
const i18n = new VueI18n({
  locale: getSuggestedLanguage(),
  fallbackLocale: fallbackLanguage,
  messages,
  silentTranslationWarn: true,
});

export function getSplittedMonth(month) {
  return splittedMonths[getSuggestedLanguage()][month];
}

export function reportHeaders(header) {
  if (getSuggestedLanguage() === 'pl') {
    switch (header) {
      case 'person':
        return 'osoba';
      case 'project':
        return 'projekt';
      case 'work time':
        return 'etat';
      case 'available time':
        return 'dostępny czas';
      case 'planned':
        return 'zaplanowano';
      default:
        return null;
    }
  } else {
    return header;
  }
}

export function getShortWeekday(weekday) {
  if (getSuggestedLanguage() === 'pl') {
    return (['Ni', 'Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So'])[weekday];
  } else {
    return (['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'])[weekday];
  }
}

export {
  i18n,
};

moment.locale(getSuggestedLanguage());
