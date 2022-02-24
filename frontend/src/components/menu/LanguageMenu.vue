<template>
  <v-menu id="component-language-menu" origin="center center" :nudge-bottom="10" transition="scale-transition" offset-y>
    <template v-slot:activator="{ on }">
      <v-btn class="ma-1" icon large text v-on="on">
        <v-icon>language</v-icon>
      </v-btn>
    </template>
    <v-list class="pa-0" dense two-line>
      <v-list-item v-for="language in supportedLanguages" :key="language.code" @click="selectLanguage(language.code)">
        <v-list-item-action>
          <v-icon v-if="language.code === current">radio_button_checked</v-icon>
          <v-icon v-else>radio_button_unchecked</v-icon>
        </v-list-item-action>
        <v-list-item-content>
          <v-list-item-title>
            {{ language.label[language.code] }}
          </v-list-item-title>
          <v-list-item-subtitle v-if="language.code !== current">
            {{ language.label[current] }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
  import { supportedLanguages, getSuggestedLanguage, setLanguage } from '../../i18n/i18n';

  export default {
    name: 'LanguageMenu',
    data() {
      return {
        supportedLanguages,
        current: getSuggestedLanguage(),
      };
    },
    methods: {
      async selectLanguage(languageCode) {
        if (languageCode !== this.current) {
          const data = { language: languageCode };
          await this.$store.dispatch('Employees/saveEmployee', data);
          setLanguage(languageCode);
        }
      },
    },
  };
</script>
