<template>
  <td :class="{'pl-1 pr-1': $vuetify.breakpoint.xs}">
    <span>{{ description }}</span>
  </td>
</template>

<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';

  export default {
    name: 'AgreementRowName',
    props: {
      agreement: { type: Object, required: true },
    },
    computed: {
      description() {
        const lang = getSuggestedLanguage();
        switch (lang) {
          case 'en': return this.agreement.descriptionEn;
          case 'pl': return this.agreement.descriptionPl;
          default: return '';
        }
      },
    },
  };
</script>
