<template>
  <v-card-text :class="{'pa-1': $vuetify.breakpoint.xs}">
    <v-list two-line>
      <v-list-item>
        <v-list-item-action>
          <v-icon>home_work</v-icon>
        </v-list-item-action>
        <v-select :items="workModeOptions"
                  v-model="employee.workMode"
                  :label="$t('Work mode')"
                  class="required"
                  :disabled="isWorkMode"/>
        <v-alert v-if="$vuetify.breakpoint.smAndUp && getWorkModeLink" class="ml-7 mt-0 mb-0" :value="true" type="info">
          <v-list-item :href="getWorkModeLink" target="_blank">
            {{ $t('You can find more info here:') + ' ' + getWorkModeLink }}
          </v-list-item>
        </v-alert>
      </v-list-item>
      <v-alert v-if="$vuetify.breakpoint.xs && getWorkModeLink" :value="true" type="info">
        <v-list-item :href="getWorkModeLink" target="_blank">
          {{ $t('Click here to find out more about work mode') }}
        </v-list-item>
      </v-alert>
      <v-list-item>
        <v-list-item-action>
          <v-icon>phone</v-icon>
        </v-list-item-action>
        <v-text-field
          v-model="employee.phone"
          :label="$t('Business phone number')"
          :rules="[validateTelephoneNumber]"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>calendar_today</v-icon></v-list-item-action>
        <v-menu ref="hiredAtVisible" v-model="hiredAtVisible" :close-on-content-click="false" offset-y max-width="300">
          <template v-slot:activator="{ on }">
            <v-text-field v-on="on"
                          :label="$t('Hire date')"
                          :value="employee.hiredAt"
                          style="width: 100%"
                          :disabled="!!firstDataHiredAt"
                          class="required"
                          readonly/>
          </template>
          <v-date-picker color="indigo" v-model="employee.hiredAt"
                         :locale="locale"
                         min="2008-01-01"
                         :first-day-of-week="$t('date.firstDayOfWeek')"
                         no-title>
            <v-spacer></v-spacer>
            <v-btn style="margin-top: -20px" text color="primary"
                   @click="$refs.hiredAtVisible.save(employee.hiredAt)">
              OK
            </v-btn>
          </v-date-picker>
        </v-menu>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>av_timer</v-icon></v-list-item-action>
        <v-text-field v-model="employee.jobTimeValue"
                      :label="$t('Worktime')"
                      class="required"
                      :rules="[validateJobTime]"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>account_box</v-icon></v-list-item-action>
        <v-select :items="contracts"
                  v-model="contract"
                  :label="$t('Contract type')"
                  class="required"
                  :disabled="Number.isInteger(firstDataContract)"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>group</v-icon></v-list-item-action>
        <v-select :items="tribes"
                  item-text="name"
                  item-value="id"
                  v-model="tribe"
                  :label="$t('Tribe')"
                  :loading="loading"
                  :no-data-text="$t('No tribes available')"
                  :disabled="!isAdmin||!isHr||!isHelpdesk"
                  class="required"/>
      </v-list-item>
      <v-list-item v-if="positions.length > 0">
        <v-list-item-action><v-icon>work</v-icon></v-list-item-action>
        <v-select :items="positions"
                  item-text="name"
                  item-value="id"
                  v-model="position"
                  :label="$t('Position')"
                  :loading="loading"
                  :no-data-text="$t('No positions available')"
                  :disabled="!isAdmin||!isHr||!isHelpdesk"
                  class="required"/>
      </v-list-item>
      <v-list-item v-if="levels.length > 0">
        <v-list-item-action><v-icon>reorder</v-icon></v-list-item-action>
        <v-select :items="levels"
                  item-text="name"
                  item-value="id"
                  v-model="level"
                  :label="$t('Level')"
                  :loading="loading"
                  :no-data-text="$t('No levels available')"
                  :disabled="!isAdmin||!isHr||!isHelpdesk"/>
      </v-list-item>
    </v-list>
  </v-card-text>
</template>

<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import { PHONE_REGEX } from '../../../util/validateRules';
  import { mapGetters, mapState } from 'vuex';

  const WORK_MODE_NOT_SET = 0;

  export default {
    name: 'FirmDataStep',
    props: {
      value: { type: Object, default: () => ({}) },
      loading: { type: Boolean, default: false },
      tribes: { type: Array, default: () => ([]) },
      positions: { type: Array, default: () => ([]) },
      levels: { type: Array, default: () => ([]) },
    },
    data() {
      return {
        changeWorkMode: false,
        firstDataHiredAt: '',
        firstDataContract: '',
        hiredAtVisible: false,
        locale: getSuggestedLanguage(),
        workModeOptions: [{
          text: this.$t('Work from office'),
          value: 1,
        }, {
          text: this.$t('Work remotely'),
          value: 2,
        }, {
          text: this.$t('Work partial remotely'),
          value: 3,
        }],
        contracts: [{
          text: this.$t('Business-to-business contract lump sum'),
          value: 1,
        }, {
          text: this.$t('Business-to-business contract hourly billing'),
          value: 2,
        }, {
          text: this.$t('Civil law contract lump sum'),
          value: 3,
        }, {
          text: this.$t('Civil law contract hourly billing'),
          value: 4,
        }, {
          text: this.$t('Employment contract'),
          value: 5,
        }, {
          text: this.$t('Outsourcing'),
          value: 5,
        }],
      };
    },
    computed: {
      ...mapState({
        entries: state => state.Config.contentConfig,
      }),
      ...mapGetters({
        isAdmin: 'Authorization/isSuperAdmin',
        isHr: 'Authorization/isHr',
        isHelpdesk: 'Authorization/isHelpdesk',
      }),
      getWorkModeLink() {
        return this.entries.filter(val => val.key === 'content.work_mode_link').map(val => val.value)[0];
      },
      isWorkMode() {
        if (this.employee.workMode === WORK_MODE_NOT_SET) {
          this.changeWorkMode = true;
        }
        return this.employee.workMode !== WORK_MODE_NOT_SET && !this.changeWorkMode;
      },
      employee: {
        get() {
          if (this.firstDataHiredAt === '') {
            this.firstDataHiredAt = this.value.hiredAt;
          }
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
      contract: {
        get() {
          if (this.firstDataContract === '') {
            this.firstDataContract = typeof this.employee.contract === 'object'
              ? this.employee.contract.id : this.employee.contract;
          }
          return typeof this.employee.contract === 'object' ? this.employee.contract.id : this.employee.contract;
        },
        set(contract) {
          const employee = { ...this.employee, contract };
          this.$emit('input', employee);
        },
      },
      tribe: {
        get() {
          return typeof this.employee.tribe === 'object' ? this.employee.tribe.id : this.employee.tribe;
        },
        set(tribe) {
          const employee = { ...this.employee, tribe };
          this.$emit('input', employee);
        },
      },
      position: {
        get() {
          return typeof this.employee.position === 'object' ? this.employee.position.id : this.employee.position;
        },
        set(position) {
          const employee = { ...this.employee, position };
          this.$emit('input', employee);
        },
      },
      level: {
        get() {
          return typeof this.employee.level === 'object' ? this.employee.level.id : this.employee.level;
        },
        set(level) {
          const employee = { ...this.employee, level };
          this.$emit('input', employee);
        },
      },
    },
    methods: {
      validateTelephoneNumber(t) {
        if (t !== undefined && t.length > 0) {
          if (typeof t === 'undefined') {
            return this.$t('Please enter correct phone number');
          }
          if (PHONE_REGEX.test(t) === false) {
            return this.$t('Please enter correct phone number');
          }
          return true;
        }
        return true;
      },
      validateJobTime(val) {
        const str = `${val}`.replace(',', '.');
        return (typeof val !== 'undefined' && str > 0 && str <= 1)
          || this.$t('Worktime must be a real number between 0 and 1');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Business phone number': 'Służbowy numer telefonu',
          'Work mode': 'Tryb pracy',
          'Hire date': 'Data rozpoczęcia współpracy',
          'Contract type': 'Forma współpracy',
          'Employment contract': 'Umowa o pracę',
          'Civil law contract lump sum': 'Umowa cywilnoprawna ryczałt',
          'Civil law contract hourly billing': 'Umowa cywilnoprawna rozliczenie godzinowe',
          'Business-to-business contract lump sum': 'Umowa business-to-business ryczałt',
          'Business-to-business contract hourly billing': 'Umowa business-to-business rozliczenie godzinowe',
          'Tribe': 'Praktyka',
          'No tribes available': 'Brak dostępnych plemion',
          'Position': 'Stanowisko',
          'No positions available': 'Brak dostępnych stanowisk',
          'Level': 'Poziom',
          'No levels available': 'Brak dostępnych poziomów',
          'Please enter correct phone number': 'Numer telefonu nie jest poprawny',
          'Worktime': 'Etat',
          'Click here to find out more about work mode': 'Kliknij tutaj by dowiedzieć się więcej o trybie pracy',
          'You can find more info here:': 'Więcej informacji znajdziesz tutaj:',
          'Work from office': 'Praca z biura',
          'Work remotely': 'Praca zdalna',
          'Work partial remotely': 'Praca cześciowo zdalna',
          'Worktime must be a real number between 0 and 1': 'Etat musi być liczbą rzeczywistą z przedziału od 0 do 1',
        },
        en: {
          Tribe: 'Practice',
        },
      },
    },
  };
</script>

<style scoped>
  .required >>> label::after {
    content: ' *';
  }
  .required >>> label.v-label--active::after {
    color: red;
  }
</style>
