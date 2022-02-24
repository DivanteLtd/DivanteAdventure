<template>
  <v-dialog id="dialog-hr-potential-employee-editor" v-model="dialogVisible" width="600">
    <v-card>
      <v-app-bar color="transparent" class="headline" flat >
        <v-row no-gutters :class="{'potential-employee-title': $vuetify.breakpoint.xs}" >
          {{ editMode ? $t('Potential person') : $t('Cooperation agreement form') }}
        </v-row>
        <v-spacer/>
        <v-btn icon @click="dialogVisible = false"><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-progress-linear height="6" indeterminate v-if="loading"/>
      <v-divider v-else/>
      <editor-content @close="close"/>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import EditorContent from '../../components/hr/potentialEmployeeEditor/EditorContent';

  export default {
    name: 'PotentialEmployeeEditor',
    components: { EditorContent },
    data() {
      return {
        dialogVisible: false,
        loading: false,
        editMode: false,
      };
    },
    methods: {
      show(employee) {
        this.editMode = typeof employee !== 'undefined';
        this.dialogVisible = true;
        this.reload(employee);
      },
      close() {
        this.dialogVisible = false;
        EventBus.$emit(eventNames.hrPersonListReload);
      },
      async reload(employee) {
        this.loading = true;
        await this.$store.dispatch('Tribes/loadTribes');
        await this.$store.dispatch('Config/loadContentConfig');
        this.loading = false;
        EventBus.$emit(eventNames.showPotentialEditorContent, employee);
      },
    },
    mounted() {
      EventBus.$on(eventNames.showPotentialEmployeeEditor, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Potential person': 'Potencjalna osoba',
          'Cooperation agreement form': 'Formularz rozpoczęcia współpracy',
        },
      },
    },
  };
</script>
<style scoped>
  .potential-employee-title{
    font-size: medium;
    line-height: initial;
  }
</style>
