<template>
  <div>
    <v-menu offset-y>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon>
          <v-icon>more_vert</v-icon>
        </v-btn>
      </template>
      <v-list>
        <v-list-item @click="createNewPerson">
          <v-list-item-title>{{ $t('Cooperation agreement form') }}</v-list-item-title>
        </v-list-item>
        <v-list-item @click="exportData">
          <v-list-item-title>{{ $t('Download persons list') }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
    <loading-dialog :visible="visible"/>
  </div>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import LoadingDialog from '../../utils/LoadingDialog';

  export default {
    name: 'PersonsListMenu',
    components: { LoadingDialog },
    props: {
      employees: { type: Array, required: true },
    },
    data() {
      return {
        visible: false,
      };
    },
    methods: {
      createNewPerson() {
        EventBus.$emit(eventNames.showPotentialEmployeeEditor);
      },
      async exportData() {
        await this.$store.dispatch('Employees/getCSV');
      },
      getGender(employee) {
        const MAN = 0;
        const WOMAN = 1;
        switch (employee.gender) {
          case MAN: return this.$t('Man');
          case WOMAN: return this.$t('Woman');
          default: return this.$t('Not filled');
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Cooperation agreement form': 'Formularz rozpoczęcia współpracy',
          'Download persons list': 'Pobierz listę osób',
          'Name and lastName': 'Imię i nazwisko',
          'Hired at': 'Data rozpoczęcia współpracy',
          'Hired to': 'Data zakończenia współpracy',
          'E-mail address': 'Adres e-mail',
          'Contract type': 'Forma współpracy',
          'Position': 'Pozycja',
          'Work mode': 'Tryb pracy',
          'Work from office': 'Praca z biura',
          'Work remotely': 'Praca zdalna',
          'Work partial remotely': 'Praca cześciowo zdalna',
          'No': 'Nie',
          'Yes': 'Tak',
          'Gender': 'Płeć',
          'City': 'Miasto',
          'Postal code': 'Kod pocztowy',
          'Street and number': 'Ulica i numer',
          'Country': 'Kraj',
          'Date of birth': 'Data urodzenia',
          'Not filled': 'Nie uzupełniono',
          'Woman': 'Kobieta',
          'Man': 'Mężczyzna',
          'Tribe': 'Praktyka',
        },
        en: {
          Tribe: 'Practice',
        },
      },
    },
  };
</script>
