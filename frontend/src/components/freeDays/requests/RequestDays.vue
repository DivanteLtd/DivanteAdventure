<template>
  <div>
    <h3>{{ resignMode ? $t('Resign from single days') : $t('List of days') }}</h3>
    <v-data-table mobile-breakpoint="0"
                  :items-per-page="100"
                  :items="days"
                  :headers="correctHeaders()"
                  hide-default-footer class="mb-2">
      <template v-slot:item="{ item }">
        <request-day-row :day="item" :resign-mode="resignMode" @update="updateDay"/>
      </template>
    </v-data-table>
    <v-col v-if="resignMode" class="request__button--resign">
      <v-btn @click="deleteRequest" color="success">{{ $t('Resign from entire request') }}</v-btn>
    </v-col>
    <attachments-list v-if="attachmentsVisible" :attachments="request.attachments"/>
  </div>
</template>

<script>
  import RequestDayRow from './RequestDayRow';
  import AttachmentsList from './AttachmentsList';
  import { leaveDaysTypes } from '../../../util/freeDays';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'RequestDays',
    components: { AttachmentsList, RequestDayRow },
    props: {
      request: { type: Object, required: true },
      resignMode: { type: Boolean, required: false },
    },
    data() { return {
      headers: [{
        text: this.$t('Date'),
        align: 'center',
        sortable: false,
      }, {
        text: this.$t('Type'),
        align: 'center',
        sortable: false,
      }],
    };},
    computed: {
      attachmentsVisible() {
        return this.request.hasOwnProperty('attachments') && this.request.attachments.length > 0;
      },
      days() {
        return this.request.days.sort((dayA, dayB) => {
          return dayA.date - dayB.date;
        });
      },
    },
    methods: {
      async deleteRequest() {
        this.$emit('dialogVisible');
        await this.$store.dispatch('FreeDays/resignFromRequest', this.request);
        EventBus.$emit(eventNames.createNewLeaveRequestAfter);
      },
      updateDay(day) {
        this.$emit('update', day);
      },
      correctHeaders() {
        if (this.request.days.length > 0 && this.request.days[0].type === leaveDaysTypes.leaveCare) {
          this.headers.concat({
            text: this.$t('Hours'),
            align: 'center',
            sortable: false,
          });
        }
        if (this.request.days.length > 0 && this.resignMode) {
          this.headers.concat({
            text: this.$t('Action'),
            align: 'center',
            sortable: false,
          });
        }
        return this.headers;
      },
    },
    i18n: { messages: {
      pl: {
        'List of days': 'Lista dni',
        'Resign from single days': 'Zrezygnuj z poszczególnych dni',
        'Resign from entire request': 'Zrezygnuj z całego wniosku',
        'Date': 'Data',
        'Type': 'Typ',
        'Hours': 'Godziny',
        'Action': 'Akcja',
      },
      en: {
        date_format: 'DD.MM.YYYY',
      },
    } },
  };
</script>
<style scoped>
  .request__button--resign {
    display: grid;
  }
</style>
