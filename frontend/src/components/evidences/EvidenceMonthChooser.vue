<template>
  <v-menu v-model="menuVisible"
          :close-on-content-click="false"
          transition="scale-transition"
          min-width="290px"
          offset-y>
    <template v-slot:activator="{ on }">
      <v-text-field v-on="on"
                    v-model="monthDisplay"
                    :label="$t('Month')"
                    readonly/>
    </template>
    <v-date-picker color="indigo" type="month"
                   v-model="containerValue"
                   @input="updateModel"
                   :locale="locale"
                   no-title/>
  </v-menu>
</template>

<script>
  import { getSuggestedLanguage } from '../../i18n/i18n';
  import { mapGetters } from 'vuex';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'EvidenceMonthChooser',
    props: {
      value: { type: String, required: false, default: null },
    },
    data() { return {
      menuVisible: false,
      monthContainer: null,
      locale: getSuggestedLanguage(),
    };},
    computed: {
      ...mapGetters({
        bestDefaultMonth: 'Evidences/bestDefaultMonth',
      }),
      containerValue: {
        get() {
          if (this.monthContainer === null) {
            this.monthContainer = this.bestDefaultMonth;
            this.$emit('input', this.bestDefaultMonth);
          }
          return this.monthContainer;
        },
        set(value) {
          this.monthContainer = value;
        },
      },
      monthDisplay() {
        return moment(this.containerValue, 'YYYY-MM').format('MMMM YYYY');
      },
    },
    watch: {
      value() {
        if (this.value !== this.containerValue) {
          this.containerValue = this.value;
        }
      },
    },
    methods: {
      updateModel() {
        this.menuVisible = false;
        this.$emit('input', this.containerValue);
      },
    },
    i18n: { messages: {
      pl: {
        Month: 'Miesiąc',
      },
    } },
  };
</script>
