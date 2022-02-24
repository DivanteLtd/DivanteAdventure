<template>
  <v-dialog id="hardware-form-dialog" v-model="dialogVisible" v-if="dialogVisible" width="600">
    <v-card>
      <v-toolbar color="transparent" class="headline" flat dense>
        <v-row>
          {{ $t('Agreement form') }}
        </v-row>
        <v-spacer/>
        <v-btn icon @click="dialogVisible = false"><v-icon>close</v-icon></v-btn>
      </v-toolbar>
      <v-progress-linear indeterminate v-if="loading"/>
      <v-divider v-else/>
      <hardware-form :item="item" @close="close"/>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import HardwareForm from './HardwareForm';

  export default {
    name: 'HardwareFormDialog',
    components: { HardwareForm },
    data() {
      return {
        dialogVisible: false,
        loading: false,
        item: {},
      };
    },
    methods: {
      show(item) {
        this.item = item;
        this.dialogVisible = true;
      },
      close() {
        this.dialogVisible = false;
        EventBus.$emit(eventNames.showGeneratedPassword);
      },
    },
    mounted() {
      EventBus.$on(eventNames.showHardwareDialog, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Agreement form': 'Formularz umowy',
        },
      },
    },
  };
</script>
