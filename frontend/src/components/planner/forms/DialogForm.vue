<template>
  <v-dialog v-model="dialogVisible" :max-width="maxWidth">
    <v-form v-model="formValid">
      <v-card>
        <v-card-title class="headline">{{ title }}</v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-row no-gutters wrap>
              <slot></slot>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn v-if="toDelete" color="red" text @click="deleteAndClose">
            {{ toDelete ? $t('Delete all') : '' }}
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn color="black" text @click="dialogVisible = false">
            {{ $t('Cancel') }}
          </v-btn>
          <v-btn color="blue" text @click="saveAndClose" :disabled="!formValid">
            {{ load ? $t('Load') : $t('Save') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-form>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'DialogForm',
    props: {
      maxWidth: { type: [Number, String], default: 600 },
      showEvent: { type: String, required: true },
      title: { type: String, required: true },
      load: { type: Boolean, default: false },
      toDelete: { type: Boolean, default: false },
    },
    data() { return {
      dialogVisible: false,
      formValid: false,
    };},
    methods: {
      deleteAndClose() {
        if (!this.formValid) {
          return;
        }
        this.$emit('toDelete');
        this.dialogVisible = false;
      },
      saveAndClose() {
        if (!this.formValid) {
          return;
        }
        this.$emit('save');
        this.dialogVisible = false;
      },
      show(data) {
        if (this.dialogVisible) {
          return;
        }
        this.$emit('show', data);
        this.dialogVisible = true;
      },
      close() {
        this.dialogVisible = false;
      },
    },
    mounted() {
      EventBus.$on(this.showEvent, this.show);
      EventBus.$on(eventNames.escapePressed, this.close);
    },
    i18n: {
      messages: {
        pl: {
          'Cancel': 'Anuluj',
          'Delete all': 'Usuń wszystkie',
          'Load': 'Wczytaj',
          'Save': 'Zapisz',
        },
      },
    },
  };
</script>
