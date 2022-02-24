<template>
  <v-chip :color="color" outlined>
    <v-icon>{{ icon }}</v-icon>
    {{ label }}
  </v-chip>
</template>

<script>
  import { evidenceStatus, getColor, getIcon } from '../../util/evidences';

  export default {
    name: 'EvidenceStatusChip',
    props: {
      evidence: { type: Object, required: true },
    },
    computed: {
      label() {
        switch(this.evidence.status) {
          case evidenceStatus.APPROVAL_NOT_REQUIRED: return this.$t('Evidence sent');
          case evidenceStatus.AWAITS_APPROVAL: return this.$t('Awaiting decision');
          case evidenceStatus.NOT_APPROVED: return this.$t('Request rejected');
          case evidenceStatus.SENT: return this.$t('Request accepted');
          default: return 'N/A';
        }
      },
      color() {
        return getColor(this.evidence.status);
      },
      icon() {
        return getIcon(this.evidence.status);
      },
    },
    i18n: { messages: {
      pl: {
        'Evidence sent': 'Ewidencja wysłana',
        'Awaiting decision': 'Oczekuje na akceptację',
        'Request accepted': 'Wniosek zaakceptowany',
        'Request rejected': 'Wniosek odrzucony',
      },
    } },
  };
</script>
