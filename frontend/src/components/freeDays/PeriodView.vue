<template>
  <v-expansion-panel>
    <v-expansion-panel-header>
      <v-row no-gutters wrap class="align-center">
        <v-col cols="8" sm="10" md="8">
          <span v-if="isCurrent">
            <b>{{ header }}</b>
          </span>
          <span v-else>
            {{ header }}
          </span>
        </v-col>
        <v-col cols="4" sm="2" md="4" class="d-flex justify-end">
          <v-tooltip left>
            <template v-slot:activator="{ on }">
              <v-btn v-if="isSuperAdmin"
                     :class="{'left': $vuetify.breakpoint.xs, 'right': $vuetify.breakpoint.smAndUp}"
                     v-on="on"
                     @click="edit()"
                     icon>
                <v-icon>edit</v-icon>
              </v-btn>
            </template>
            {{ $t('Edit') }}
          </v-tooltip>
          <v-tooltip left>
            <template v-slot:activator="{ on }">
              <v-btn v-if="isSuperAdmin"
                     :class="{'left': $vuetify.breakpoint.xs, 'right': $vuetify.breakpoint.smAndUp}"
                     v-on="on"
                     @click="dialogDelete = true"
                     icon>
                <v-icon>delete</v-icon>
              </v-btn>
            </template>
            {{ $t('Delete') }}
          </v-tooltip>
        </v-col>
        <v-dialog v-model="dialogDelete" v-if="dialogDelete" max-width="450">
          <v-card id="free-days-period-dialog">
            <v-card-title class="headline">
              {{ $t('Period deletion') }}
            </v-card-title>
            <v-card-text>
              {{ infoText() }}
            </v-card-text>
            <v-card-actions>
              <v-btn text="text" v-if="!isDelete" @click="dialogDelete = false">
                {{ $t('Cancel') }}
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn v-if="isDelete" text @click="dialogDelete = false">
                {{ $t('No') }}
              </v-btn>
              <v-btn v-if="isDelete" color="red" text @click="deletePeriod">
                {{ $t('Yes') }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-row>
    </v-expansion-panel-header>
    <v-expansion-panel-content :class="{'period-view__content': $vuetify.breakpoint.xs}">
      <period-info :period="period"/>
      <request-table :period="period"/>
    </v-expansion-panel-content>
  </v-expansion-panel>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import PeriodInfo from './info/PeriodInfo';
  import RequestTable from './requests/RequestTable';
  import { EventBus, eventNames } from '../../eventbus';
  import { mapGetters } from 'vuex';
  import { leaveRequestsStatuses } from '../../util/freeDays';

  export default {
    name: 'PeriodView',
    components: { RequestTable, PeriodInfo },
    props: {
      period: { type: Object, required: true },
    },
    data() {
      return {
        dialogDelete: false,
        isDelete: false,
      };
    },
    computed: {
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      isCurrent() {
        const start = moment(this.period.dateFromMoment).startOf('day');
        const end = moment(this.period.dateToMoment).endOf('day');
        return moment.range(start, end).contains(moment());
      },
      header() {
        const format = this.$t('period_date_format');
        const timeFrom = this.period.dateFromMoment.format(format);
        const timeTo = this.period.dateToMoment.format(format);
        return this.$t('period_from_to').replace('{FROM}', timeFrom).replace('{TO}', timeTo);
      },
    },
    methods: {
      edit() {
        this.period.isEdit = true;
        EventBus.$emit(eventNames.createEditNewPeriod, this.period);
      },
      deletePeriod() {
        this.$store.dispatch('FreeDays/deletePeriod', this.period.id).then(() => {
          this.dialogDelete = false;
          EventBus.$emit(eventNames.reloadPeriods);
        });
      },
      infoText() {
        const deletableStatuses = [
          leaveRequestsStatuses.resigned,
          leaveRequestsStatuses.rejected,
        ];
        const requests = this.period.requests.filter(request => !deletableStatuses.includes(request.status));
        if (requests.length > 0) {
          this.isDelete = false;
          return this.$t('You can not delete this period because of existing requests.');
        } else {
          this.isDelete = true;
          return this.$t('Are you sure you want to delete this period?');
        }
      },
    },
    i18n: { messages: {
      pl: {
        'period_from_to': 'Okres od {FROM} do {TO}',
        'Edit': 'Edytuj',
        'Cancel': 'Anuluj',
        'Delete': 'Usuń',
        'Yes': 'Tak',
        'No': 'Nie',
        'Period deletion': 'Usuwanie okresu',
        'You can not delete this period because of existing requests.':
          'Nie możesz usunąć okresu ponieważ są stworzone wnioski.',
        'Are you sure you want to delete this period?': 'Czy na pewno chcesz usunąć ten okres?',
      },
      en: {
        period_from_to: 'Period from {FROM} to {TO}',
        period_date_format: 'D MMMM YYYY',
      },
    } },
  };
</script>
<style>
  .period-view__content .v-expansion-panel-content__wrap{
    padding: 0 !important;
  }
</style>
