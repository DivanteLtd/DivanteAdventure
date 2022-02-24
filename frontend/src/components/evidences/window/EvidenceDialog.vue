<template>
  <v-dialog v-model="dialogVisible" width="1000">
    <v-card :class="{'evidence-dialog': $vuetify.breakpoint.xs}">
      <v-card-title class="headline">
        <span>{{ $t('window_title', {ID: evidence.id }) }}</span>
        <v-spacer/>
        <evidence-status-chip :evidence="evidence"/>
      </v-card-title>
      <v-card-text :class="{'pa-0': $vuetify.breakpoint.xs}">
        <evidence-dialog-content :evidence="evidence"/>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn v-if="showAcceptButtons" color="green" text @click="accept">
          {{ $t('Accept') }}
        </v-btn>
        <v-btn v-if="showAcceptButtons" color="red" text @click="reject">
          {{ $t('Reject') }}
        </v-btn>
        <v-btn color="black" text @click="dialogVisible = false">
          {{ $t('Close') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import EvidenceStatusChip from '../EvidenceStatusChip';
  import EvidenceDialogContent from './EvidenceDialogContent';
  import { evidenceStatus } from '../../../util/evidences';

  export default {
    name: 'EvidenceDialog',
    components: { EvidenceDialogContent, EvidenceStatusChip },
    data() { return {
      dialogVisible: false,
      showAcceptButtons: false,
      evidence: { employee: {}, overtimeManager: {} },
    };},
    methods: {
      accept() {
        this.changeStatus(evidenceStatus.SENT);
      },
      reject() {
        this.changeStatus(evidenceStatus.NOT_APPROVED);
      },
      changeStatus(status) {
        EventBus.$emit(eventNames.requestStatusUpdateBefore, { id: this.evidence.id, type: 'overtime' });
        this.dialogVisible = false;
        this.$store.dispatch('Evidences/changeOvertimeStatus', { evidenceId: this.evidence.id, status }).then(() => {
          EventBus.$emit(eventNames.requestStatusUpdate, { id: this.evidence.id, type: 'overtime' });
        });
      },
      show(evidence) {
        this.showDialog(evidence, false);
      },
      showForAcceptance(evidence) {
        this.showDialog(evidence, true);
      },
      showDialog(evidence, showAccept) {
        if (this.dialogVisible) {
          return;
        }
        this.evidence = evidence;
        this.showAcceptButtons = showAccept;
        this.dialogVisible = true;
      },
    },
    mounted() {
      EventBus.$on(eventNames.showEvidenceWindow, this.show);
      EventBus.$on(eventNames.showEvidenceWindowForAcceptance, this.showForAcceptance);
    },
    i18n: { messages: {
      pl: {
        Close: 'Zamknij',
        Accept: 'Zaakceptuj',
        Reject: 'Odrzuć',

        window_title: 'Wniosek #{ID}',
      },
      en: {
        window_title: 'Request #{ID}',
      },
    } },
  };
</script>
<style>
  .evidence-dialog .v-card__text{
    padding: 0;
  }
</style>
