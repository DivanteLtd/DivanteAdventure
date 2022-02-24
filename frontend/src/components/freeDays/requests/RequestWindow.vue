<template>
  <v-dialog v-model="dialogVisible" width="1100" class="ma-2">
    <v-card>
      <v-card-title class="headline">
        <span :class="{'window-title-mobile': $vuetify.breakpoint.xs}">{{ $t('window_title', {ID: request.id}) }}</span>
        <v-spacer/>
        <status-chip :request="request"/>
      </v-card-title>
      <v-card-text :class="{'pa-1': $vuetify.breakpoint.xs}">
        <request-window-content
          :request="request"
          :acceptance-mode="acceptanceMode"
          :resign-mode="resignMode"
          @resignButton="updateButton"
          @dialogVisible="dialogVisible = false"
        />
      </v-card-text>
      <v-card-actions>
        <v-row no-gutters row wrap class="button-row">
          <v-col cols="4" md="2" lg="1" :class="{'mx-3': $vuetify.breakpoint.lgAndUp}">
            <v-btn color="black" text @click="dialogVisible = false"
                   :class="{'button-mobile mr-6': $vuetify.breakpoint.xs}">
              {{ $t('Cancel') }}
            </v-btn>
          </v-col>
          <v-col v-if="acceptanceMode" cols="4" md="2" lg="1" class="button"
                 :class="{'mx-3': $vuetify.breakpoint.lgAndUp}">
            <v-btn color="red" text @click="reject" :class="{'button-mobile mr-6': $vuetify.breakpoint.xs}">
              {{ $t('Reject') }}
            </v-btn>
          </v-col>
          <v-col v-if="acceptanceMode" cols="4" md="2" lg="1" class="button"
                 :class="{'mx-5': $vuetify.breakpoint.lgAndUp}">
            <v-btn color="green" text @click="accept" :class="{'button-mobile mr-6': $vuetify.breakpoint.xs}">
              {{ $t('Accept') }}
            </v-btn>
          </v-col>
          <v-col v-if="resignMode" cols="4" md="2" lg="1" class="button-row"
                 :class="{'mx-2': $vuetify.breakpoint.lgAndUp}">
            <v-btn color="primary"
                   text
                   :disabled="!resignButton"
                   @click="save"
                   :class="{'button-mobile': $vuetify.breakpoint.xs}"
            >
              {{ $t('Save') }}
            </v-btn>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import RequestWindowContent from './RequestWindowContent';
  import StatusChip from './StatusChip';
  import { mapGetters, mapState } from 'vuex';
  import { leaveRequestsStatuses, leaveDaysStatuses, leaveDaysTypes } from '../../../util/freeDays';

  export default {
    name: 'RequestWindow',
    components: { StatusChip, RequestWindowContent },
    data() { return {
      dialogVisible: false,
      request: { manager: {}, days: [], employee: {} },
      tmpRequest: { manager: {}, days: [], employee: {} },
      acceptanceMode: false,
      resignButton: false,
      resignMode: false,
      loading: true,
    };},
    computed: {
      ...mapState({
        myPeriods: state => state.FreeDays.myPeriods,
      }),
      ...mapGetters({
        userId: 'Authorization/getUserId',
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
    },
    methods: {
      updateButton() {
        this.resignButton = this.request.days.filter(val => val.deleted).length > 0;
      },
      accept() {
        this.dialogVisible = false;
        EventBus.$emit(eventNames.requestStatusUpdateBefore, { id: this.request.id, type: 'leave' });
        this.$store.dispatch('FreeDays/acceptRequest', this.request).then(() => {
          EventBus.$emit(eventNames.requestStatusUpdate, { id: this.request.id, type: 'leave', comment: this.request.comment });
        });
      },
      async save() {
        await this.$store.dispatch('FreeDays/updateRequest', this.request);
        if (this.request.status !== leaveRequestsStatuses.pendingResignation) {
          this.request.days = this.request.days.filter(val => !val.deleted);
          this.$store.commit('showSnackbar', { text: this.$t('snackbar_resign'), color: 'success' });
        } else {
          this.request.days.forEach(val => {
            if (val.deleted) {
              val.status = leaveDaysStatuses.pendingResignation;
            }
          });
          this.$store.commit('showSnackbar', { text: this.$t('snackbar_resign_pending'), color: 'success' });
          await this.$store.dispatch('FreeDays/loadMyPeriods');
        }
        if (this.request.days[0].type === leaveDaysTypes.overtime) {
          await this.$store.dispatch('FreeDays/loadMyPeriods');
        }
        this.dialogVisible = false;
      },
      reject() {
        this.dialogVisible = false;
        EventBus.$emit(eventNames.requestStatusUpdateBefore, { id: this.request.id, type: 'leave' });
        this.$store.dispatch('FreeDays/rejectRequest', this.request).then(() => {
          EventBus.$emit(eventNames.requestStatusUpdate, { id: this.request.id, type: 'leave', comment: this.request.comment });
        });
      },
      async show(data) {
        if (!this.dialogVisible) {
          this.request = data;
          this.resignMode = false;
          this.resignButton = false;
          this.acceptanceMode = false;
          this.dialogVisible = true;
        }
      },
      showForAcceptance(data) {
        if (!this.dialogVisible) {
          this.request = data;
          this.resignButton = false;
          this.resignMode = false;
          this.acceptanceMode = true;
          this.dialogVisible = true;
        }
      },
      showForResign(data) {
        if (!this.dialogVisible) {
          this.request = data;
          this.request.days = this.request.days.map(obj => ({ ...obj, deleted: false }));
          this.resignMode = true;
          this.acceptanceMode = false;
          this.resignButton = false;
          this.dialogVisible = true;
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.showRequestDetails, this.show);
      EventBus.$on(eventNames.showRequestDetailsForAcceptance, this.showForAcceptance);
      EventBus.$on(eventNames.showRequestDetailsForResign, this.showForResign);
    },
    i18n: { messages: {
      pl: {
        Cancel: 'Anuluj',
        Confirm: 'Potwierdź',
        Accept: 'Zaakceptuj',
        Reject: 'Odrzuć',
        Save: 'Zapisz',
        window_title: 'Wniosek #{ID}',
        snackbar_resign: 'Poprawnie zrezygnowano z dnia/dni',
        snackbar_resign_pending: 'Rezygnacja wysłana do przełożonego',
      },
      en: {
        window_title: 'Request #{ID}',
        snackbar_resign: 'Resign from days/day correctly',
        snackbar_resign_pending: 'Resign request has been sent to manager',
      },
    } },
  };
</script>
<style scoped>
  .button-row{
    justify-content: flex-end;
  }
  .button{
    display: flex;
    justify-content: center;
  }
  .button-mobile{
    font-size: small;
  }
</style>
