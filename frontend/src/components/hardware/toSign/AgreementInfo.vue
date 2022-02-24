<template>
  <v-list two-line>
    <v-list-item>
      <v-list-item-action><v-icon color="primary">person_outline</v-icon></v-list-item-action>
      <v-list-item-content>
        <v-list-item-title>{{ agreement.name }} {{ agreement.lastName }}</v-list-item-title>
        <v-list-item-subtitle>{{ $t('Hardware receiver') }}</v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>
    <v-list-item>
      <v-list-item-action><v-icon color="primary">{{ icon }}</v-icon></v-list-item-action>
      <v-list-item-content>
        <v-list-item-title>{{ agreement.manufacturer }} {{ agreement.model }}</v-list-item-title>
        <v-list-item-subtitle>{{ $t('Hardware brand and model') }}</v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>
    <v-list-item v-if="agreement.serialNumber">
      <v-list-item-action><v-icon color="primary">confirmation_number</v-icon></v-list-item-action>
      <v-list-item-content>
        <v-list-item-title>{{ agreement.serialNumber }}</v-list-item-title>
        <v-list-item-subtitle>{{ $t('Hardware serial number') }}</v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>
  </v-list>
</template>

<script>
  export default {
    name: 'AgreementInfo',
    props: {
      agreement: { type: Object, required: true },
    },
    computed: {
      icon() {
        switch (this.agreement.category) {
          case 'Laptop': return 'laptop';
          case 'Mini PC': return 'desktop_windows';
          case 'Monitor': return 'tv';
          case 'Słuchawki': return 'headset';
          case 'Telefon': return 'smartphone';
          default: return 'devices_other';
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Hardware receiver': 'Odbiorca sprzętu',
          'Hardware brand and model': 'Marka i model sprzętu',
          'Hardware serial number': 'Numer seryjny',
        },
      },
    },
  };
</script>
