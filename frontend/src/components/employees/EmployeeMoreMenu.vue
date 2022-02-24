<template>
  <div>
    <v-menu v-if="moreMenu.length > 0" offset-y>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon>
          <v-icon>more_vert</v-icon>
        </v-btn>
      </template>
      <v-list>
        <v-list-item v-for="(item, index) in moreMenu" :key="index" @click="item.clickAction">
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
    <loading-dialog :visible="visible"/>
  </div>
</template>

<script>
  import { mapGetters, mapState } from 'vuex';
  import exportList from '../../util/exporter';
  import moment from '@divante-adventure/work-moment';
  import LoadingDialog from '../utils/LoadingDialog';

  export default {
    name: 'EmployeeMoreMenu',
    components: { LoadingDialog },
    data() {
      return {
        visible: false,
      };
    },
    computed: {
      ...mapGetters({
        isTribeMaster: 'Authorization/isTribeMaster',
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      ...mapState({
        employees: state => state.Employees.employees,
        freeDaysReport: state => state.FreeDays.freeDaysReport,
      }),
      moreMenu() {
        const menu = [];
        if (this.isTribeMaster) {
          menu.push({
            title: this.$t('Download people list'),
            clickAction: this.exportEmployees,
          });
        }
        if (this.isSuperAdmin) {
          menu.push({
            title: this.$t('Download unavailability days report'),
            clickAction: this.exportLeaveList,
          });
        }
        return menu;
      },
    },
    methods: {
      exportLeaveList() {
        this.visible = true;
        this.$store.dispatch('FreeDays/createFreeDaysReport').finally(() => {
          const list = this.freeDaysReport;
          const headers = [{
            label: this.$t('Name and lastName'),
            value: period => (period.employeeName || this.$t('Empty')),
          }, {
            label: this.$t('Contract'),
            value: period => (this.$t(period.contractName) || this.$t('Empty')),
          }, {
            label: this.$t('Period from'),
            value: period => (period.periodFrom || this.$t('Empty')),
          }, {
            label: this.$t('Period to'),
            value: period => (period.periodTo || this.$t('Empty')),
          }, {
            label: this.$t('Days paid owned'),
            value: period => (period.freedaysOwed || this.$t('Empty')),
          }, {
            label: this.$t('Days paid used'),
            value: period => (period.freeDaysPaidUsed || this.$t('Empty')),
          }, {
            label: this.$t('Days unpaid used'),
            value: period => (period.freeDaysUnpaidUsed || this.$t('Empty')),
          }, {
            label: this.$t('Days on demand used'),
            value: period => (period.freeDaysRequest || this.$t('Empty')),
          }, {
            label: this.$t('Occasional days used'),
            value: period => (period.freeDaysOccasional || this.$t('Empty')),
          }, {
            label: this.$t('Child care days used'),
            value: period => (period.freeDaysCare || this.$t('Empty')),
          }, {
            label: this.$t('Days paid'),
            value: period => (typeof period.freeDaysPaidList === 'undefined' ? this.$t('Empty')
              : this.getDates(period.freeDaysPaidList)),
          }, {
            label: this.$t('Days unpaid'),
            value: period => (typeof period.freeDaysUnpaidList === 'undefined' ? this.$t('Empty')
              : this.getDates(period.freeDaysUnpaidList)),
          }, {
            label: this.$t('Days on demand'),
            value: period => (typeof period.freeDaysRequestList === 'undefined' ? this.$t('Empty')
              : this.getDates(period.freeDaysRequestList)),
          }, {
            label: this.$t('Occasional days'),
            value: period => (typeof period.freedaysOccasionalList === 'undefined' ? this.$t('Empty')
              : this.getDates(period.freedaysOccasionalList)),
          }, {
            label: this.$t('Child care days'),
            value: period => (typeof period.freedaysCareList === 'undefined' ? this.$t('Empty')
              : this.getDates(period.freedaysCareList)),
          }, {
            label: this.$t('Sick leave days'),
            value: period => (typeof period.sickLeaveDaysList === 'undefined' ? this.$t('Empty')
              : this.getDates(period.sickLeaveDaysList)),
          }, {
            label: this.$t('Sick leave days owned'),
            value: period => (period.sickleavedaysOwed || this.$t('Empty')),
          }, {
            label: this.$t('Sick leave days used'),
            value: period => (period.sickLeaveDaysUsed || this.$t('Empty')),
          }];
          exportList(list, headers, 'freeDays.csv');
          this.visible = false;
        });
      },
      async exportEmployees() {
        await this.$store.dispatch('Employees/getCSV');
      },
      getDates(periodDates) {
        return periodDates.split(',').map(val => moment(val).format('YYYY-MM-DD')).join(', ');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Download people list': 'Pobierz listę osób',
          'Download unavailability days report': 'Pobierz raport dni niedostępności',
          'ID': 'ID',
          'Name and lastName': 'Imię i nazwisko',
          'Hired at': 'Data rozpoczęcia współpracy',
          'E-mail address': 'Adres e-mail',
          'City': 'Miasto',
          'Postal code': 'Kod pocztowy',
          'Street and number': 'Ulica i numer',
          'Country': 'Kraj',
          'Contract type': 'Forma współpracy',
          'Position': 'Pozycja',
          'Work mode': 'Tryb pracy',
          'Tribe': 'Praktyka',
          'Work from office': 'Praca z biura',
          'Work remotely': 'Praca zdalna',
          'Work partial remotely': 'Praca cześciowo zdalna',
          'Empty': 'Brak',
          'Yes': 'Tak',
          'No': 'Nie',
          'Contract': 'Kontrakt',
          'Period from': 'Okres od',
          'Period to': 'Okres do',
          'Days paid owned': 'Przysługujące dni płatne',
          'Days paid used': 'Wykorzystane dni płatne',
          'Days unpaid used': 'Wykorzystane bezpłatne',
          'Days on demand used': 'Wykorzystane dni na żądanie',
          'Occasional days used': 'Wykorzystane dni okolicznościowe',
          'Child care days used': 'Wykorzystane dni opiekuńcze',
          'Days paid': 'Dni płatne',
          'Days unpaid': 'Dni bezpłatne',
          'Days on demand': 'Dni urlopowe na żądanie',
          'Occasional days': 'Dni okolicznościowe',
          'Child care days': 'Dni opieki nad dzieckiem',
          'Sick leave days': 'Dni chorobowe',
          'Sick leave days owned': 'Przysługujące dni chorobowe',
          'Sick leave days used': 'Wykorzystane dni chorobowe',
          'CoE': 'UoP',
          'CLC LUMP SUM': 'UCP RYCZAŁT',
          'CLC HOURLY': 'UCP GODZINOWE',
          'B2B LUMP SUM': 'B2B RYCZAŁT',
          'B2B HOURLY': 'B2B GODZINOWE',
        },
        en: {
          Tribe: 'Practice',
        },
      },
    },
  };
</script>
