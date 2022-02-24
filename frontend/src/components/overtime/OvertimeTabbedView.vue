<template>
  <v-card>
    <v-card-text>
      <v-tabs fixed-tabs v-model="tab">
        <v-tab>{{ $t('Overtime form') }}</v-tab>
        <v-tab>{{ $t('Archive') }}</v-tab>
      </v-tabs>
      <v-tabs-items touchless v-model="tab">
        <v-tab-item>
          <new-overtime-form v-if="loaded"/>
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
  import EvidenceArchive from '../evidences/EvidenceArchive';
  import NewOvertimeForm from './NewOvertimeForm';
  import NewEvidenceFormLoading from '../evidences/NewEvidenceFormLoading';

  export default {
    name: 'OvertimeTabbedView',
    components: { NewEvidenceFormLoading, NewOvertimeForm, EvidenceArchive },
    props: {
      loaded: { type: Boolean, default: false },
    },
    data() { return {
      tab: null,
    };},
    i18n: { messages: {
      pl: {
        'Overtime form': 'Formularz nadgodzin',
        'Archive': 'Archiwum',
      },
    } },
  };
</script>
