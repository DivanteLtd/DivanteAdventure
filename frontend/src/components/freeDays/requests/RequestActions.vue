<template>
  <div class="request--actions d-flex">
  </div>
</template>

<script>
  import { leaveDaysTypes, leaveRequestsStatuses } from '../../../util/freeDays';
  import { eventNames, EventBus } from '../../../eventbus';
  import { mapGetters } from 'vuex';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'RequestActions',
    props: {
      request: { type: Object, required: true },
      disableDelete: { type: Boolean, default: false },
    },
    data() {
      return {
        loadingResign: false,
        loadingDelete: false,
        deleteDialogVisible: false,
      };
    },
    computed: {
      ...mapGetters({
        userId: 'Authorization/getUserId',
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      resignTooltip() {
        if (this.canResign && this.request.status === leaveRequestsStatuses.pendingResignation) {
          return this.$t('Withdraw resignation');
        }
        else if (this.canResign) {
          return this.$t('Resign');
        }
        else {
          return this.$t('180 days after accepting request has already passed; only ADM department can now cancel that request.');
        }
      },
      showResignButton() {
        const isCorrectUser = this.userId === this.request.employee.id;
        const correctStatuses = [
          leaveRequestsStatuses.pending,
          leaveRequestsStatuses.accepted,
          leaveRequestsStatuses.pendingResignation,
          leaveRequestsStatuses.planned,
        ];
        const isCorrectStatus = correctStatuses.includes(this.request.status);
        return isCorrectStatus && isCorrectUser;
      },
      canResign() {
        const resignButtonVisible = this.showResignButton;
        const isAccepted = this.request.status === leaveRequestsStatuses.accepted;
        const isCorrectTime = !isAccepted || moment().diff(moment(this.request.acceptedAt), 'days') < 180;
        return resignButtonVisible && isCorrectTime;
      },
    },
    methods: {
      async resign() {
        if ((this.canResign && this.request.status === leaveRequestsStatuses.pendingResignation)
          || this.request.days.length === 1) {
          this.loadingResign = true;
          await this.$store.dispatch('FreeDays/resignFromRequest', this.request);
          this.loadingResign = false;
          await EventBus.$emit(eventNames.createNewLeaveRequestAfter);
          if (this.request.days[0].type === leaveDaysTypes.overtime) {
            window.location.reload();
          }
        } else {
          EventBus.$emit(eventNames.showRequestDetailsForResign, this.request);
        }
      },
      async deleteRequest() {
        this.loadingDelete = true;
        await this.$store.dispatch('FreeDays/deleteRequest', this.request.id);
        this.loadingDelete = false;
        EventBus.$emit(eventNames.createNewLeaveRequestAfter);
      },
    },
    i18n: { messages: {
      pl: {
        'Resign': 'Zrezygnuj',
        'Withdraw resignation': 'Cofnij rezygnację',
        'Delete request': 'Usuń wniosek',
        '180 days after accepting request has already passed; only ADM department can now cancel that request.': 'Minęło 180 dni od zaakceptowania wniosku. Teraz może zostać anulowany tylko przez dział ADM.',
        'confirmation_question': 'Czy na pewno chcesz usunąć wniosek?',
      },
      en: {
        confirmation_question: 'Do you really want to delete this request?',
      },
    } },
  };
</script>
<style scoped>
  .request--actions {
    text-align: center;
  }
</style>
