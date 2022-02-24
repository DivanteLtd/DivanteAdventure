<template>
  <v-menu offset-y>
    <template v-slot:activator="{ on }">
      <v-btn v-on="on" icon>
        <v-icon>more_vert</v-icon>
      </v-btn>
    </template>
    <v-list>
      <v-list-item v-for="(item, index) in moreMenu" :key="index" @click="item.clickAction(report = item.title)">
        <v-list-item-title>{{ item.title }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
  import exportList from '../../util/exporter';
  import { mapState } from 'vuex';
  import { EventBus, eventNames } from '../../eventbus';
  import moment from '@divante-adventure/work-moment';


  const GDPR_TYPE = 0;
  const ISO_TYPE = 3;

  export default {
    name: 'AcceptationMoreMenu',
    data() {
      return {
        report: '',
      };
    },
    computed: {
      ...mapState({
        marketingAcceptationList: state => state.Agreements.marketingAcceptationList,
        marketingConsents: state => state.Agreements.marketingConsents,
        GDPRAcceptations: state => state.Agreements.GDPRAcceptationList,
        iSOAcceptations: state => state.Agreements.iSOAcceptationList,
        agreements: state => state.Agreements.agreements,
      }),
      moreMenu() {
        const menu = [];
        menu.push({
          title: this.$t('GDPR - accepted'),
          clickAction: this.exportReport,
        });
        menu.push({
          title: this.$t('GDPR - not accepted'),
          clickAction: this.exportReport,
        });
        menu.push({
          title: this.$t('ISO - accepted'),
          clickAction: this.exportReport,
        });
        menu.push({
          title: this.$t('ISO - not accepted'),
          clickAction: this.exportReport,
        });
        menu.push({
          title: this.$t('Marketing - filled'),
          clickAction: this.exportReport,
        });
        menu.push({
          title: this.$t('Marketing - not filled'),
          clickAction: this.exportReport,
        });
        return menu;
      },
    },
    methods: {
      async exportReport() {
        EventBus.$emit(eventNames.showLoadingDialog, true);
        let list = [];
        switch(this.report) {
          case this.$t('GDPR - not accepted'):
            list = this.GDPRAcceptations
              .filter(val => val.description.length !== this.agreements.filter(val => val.type === GDPR_TYPE).length);
            break;
          case this.$t('GDPR - accepted'):
            list = this.GDPRAcceptations
              .filter(val => val.description.length === this.agreements.filter(val => val.type === GDPR_TYPE).length);
            break;
          case this.$t('ISO - not accepted'):
            list = this.iSOAcceptations
              .filter(val => val.description.length !== this.agreements.filter(val => val.type === ISO_TYPE).length);
            break;
          case this.$t('ISO - accepted'):
            list = this.iSOAcceptations
              .filter(val => val.description.length === this.agreements.filter(val => val.type === ISO_TYPE).length);
            break;
          case this.$t('Marketing - filled'):
            list = this.marketingAcceptationList;
            break;
          case this.$t('Marketing - not filled'):
            list = this.marketingAcceptationList.filter(val => val.description[0] === '');
            break;
          default:
            list = '';
        }
        const headers = [{
          label: this.$t('LastName'),
          value: val => (val.name),
        }, {
          label: this.$t('Name'),
          value: val => (val.lastName),
        }, {
          label: 'Email',
          value: val => (val.email),
        }];
        if (this.report === this.$t('Marketing - filled')) {
          this.marketingConsents.forEach(val => {
            headers.push({
              label: val.descriptionPl,
              value: value => (value.description.filter(value2 => value2 === val.id).length > 0 ? 'TAK' : 'NIE'),
            });
          });
        }
        if (this.report === this.$t('ISO - accepted')) {
          this.agreements.forEach(val => {
            if (val.type === ISO_TYPE) {
              headers.push({
                label: val.descriptionPl,
                value: value => (value.updatedAt),
              });
            }
          });
        }
        const name = `${this.report.concat(' ') + moment().format('DD-MM-YYYY')}`.concat('.csv');
        await exportList(list, headers, name);
        setTimeout(() => {
          EventBus.$emit(eventNames.showLoadingDialog, false);
        }, 1000);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Name': 'Imię',
          'LastName': 'Nazwisko',
          'GDPR - not accepted': 'RODO - nie zaakceptowane',
          'GDPR - accepted': 'RODO - zaakceptowane',
          'ISO - not accepted': 'ISO - nie zaakceptowane',
          'ISO - accepted': 'ISO - zaakceptowane',
          'Marketing - filled': 'Marketingowe - uzupełnione',
          'Marketing - not filled': 'Marketingowe - nie uzupełnione',
        },
      },
    },
  };
</script>
