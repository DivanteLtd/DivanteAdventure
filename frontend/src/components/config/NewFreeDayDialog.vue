<template>
  <v-dialog id="new-free-day-dialog" v-model="dialogVisible" width="600" persistent>
    <v-card>
      <v-card-title class="title">
        <span>{{ $t('Create new free day') }}</span>
        <v-spacer/>
        <v-btn @click="dialogVisible = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <v-text-field v-model="name" :label="$t('Name')"/>
        <v-checkbox v-model="repeating" :label="$t('Repeating every year')"/>
        <v-menu v-model="datePickerVisible" offset-y :close-on-content-click="false">
          <template v-slot:activator="{ on }">
            <v-text-field v-on="on" v-model="date" :label="$t('Date')" readonly/>
          </template>
          <v-date-picker :first-day-of-week="$t('date.firstDayOfWeek')" color="indigo" v-model="date" :locale="locale"
                         landscape/>
        </v-menu>
      </v-card-text>
      <v-card-actions>
        <v-btn color="success" @click="save" :loading="loading" block>{{ $t('Save') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import { getSuggestedLanguage } from '../../i18n/i18n';

  export default {
    name: 'NewFreeDayDialog',
    props: {
      value: { type: Boolean, required: true },
    },
    data() {
      return {
        locale: getSuggestedLanguage(),
        date: moment().format('YYYY-MM-DD'),
        name: '',
        repeating: true,
        datePickerVisible: false,
        loading: false,
      };
    },
    computed: {
      dialogVisible: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    methods: {
      async save() {
        this.loading = true;
        const { date, repeating, name } = this;
        await this.$store.dispatch('Config/createFreeDay', { date, repeating, name });
        this.loading = false;
        this.dialogVisible = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Create new free day': 'Utwórz nowy dzień wolny od pracy',
          'Name': 'Nazwa',
          'Date': 'Data',
          'Repeating every year': 'Powtarza się każdego roku',
          'Save': 'Zapisz',
        },
      },
    },
  };
</script>
