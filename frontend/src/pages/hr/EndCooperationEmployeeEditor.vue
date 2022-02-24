<template>
  <v-dialog id="dialog-hr-end-cooperation-employee-editor" v-model="modelWrapper" width="600">
    <v-card>
      <v-app-bar color="transparent" class="headline" flat dense>
        <v-row no-gutters :class="{'end-cooperation__title': $vuetify.breakpoint.xs}">
          {{ $t('Termination agreement form') }}
        </v-row>
        <v-spacer/>
        <v-btn icon @click="modelWrapper = false"><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-divider/>
      <editor-content :employee="employee" :is-edit="isEdit" @close="close"/>
    </v-card>
  </v-dialog>
</template>

<script>
  import EditorContent from '../../components/hr/endCooperationEmployeeEditor/EditorContent';

  export default {
    name: 'EndCooperationEmployeeEditor',
    components: { EditorContent },
    props: {
      value: { type: Boolean, required: true },
      isEdit: { type: Boolean, required: true },
      employee: { type: Object, required: true },
    },
    computed: {
      modelWrapper: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    methods: {
      close() {
        this.modelWrapper = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Termination agreement form': 'Formularz zakończenia współpracy',
        },
      },
    },
  };
</script>
<style scoped>
  .end-cooperation__title{
    font-size: medium;
    line-height: initial;
  }
</style>
