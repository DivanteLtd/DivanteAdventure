<template>
  <v-card>
    <v-card-text>
      <v-tabs v-model="tab" centered>
        <v-tab>{{ $t('New evidence') }}</v-tab>
        <v-tab>{{ $t('Archive') }}</v-tab>
      </v-tabs>
      <v-tabs-items v-model="tab" touchless>
        <v-tab-item>
          <new-evidence-form v-if="loaded"/>
          <new-evidence-form-loading v-else/>
        </v-tab-item>
        <v-tab-item>
          <evidence-archive id="tab-archived-evidences" :loaded="loaded"/>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </v-card>
</template>

<script>
  import EvidenceArchive from './EvidenceArchive';
  import NewEvidenceForm from './NewEvidenceForm';
  import NewEvidenceFormLoading from './NewEvidenceFormLoading';

  export default {
    name: 'EvidencesTabbedView',
    components: { NewEvidenceFormLoading, NewEvidenceForm, EvidenceArchive },
    props: {
      loaded: { type: Boolean, default: false },
    },
    data() {
      return {
        tab: null,
      };
    },
    i18n: { messages: {
      pl: {
        'New evidence': 'Nowa ewidencja',
        'Archive': 'Archiwum',
      },
    } },
  };
</script>
