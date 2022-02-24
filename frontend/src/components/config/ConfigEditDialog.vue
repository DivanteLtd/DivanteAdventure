<template>
  <v-dialog id="config-edit-dialog" v-model="dialogVisible" width="600" persistent>
    <v-card>
      <v-card-title class="title">
        <span>{{ $t('Edit configuration') }}</span>
        <v-spacer/>
        <v-btn @click="dialogVisible = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <v-text-field v-model="displayGroup" :label="$t('Group')" disabled/>
        <v-text-field v-model="displayKey" :label="$t('Key')" disabled/>
        <v-text-field v-model="input" :label="$t('Value')"/>
      </v-card-text>
      <v-card-actions>
        <v-btn color="success" @click="save" :loading="loading" block>{{ $t('Save') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'ConfigEditDialog',
    props: {
      value: { type: Boolean, required: true },
      configKey: { type: String, required: true },
      displayGroup: { type: String, required: true },
      displayKey: { type: String, required: true },
      configValue: { type: String, required: true },
    },
    data() {
      return {
        input: this.configValue,
        loading: false,
      };
    },
    computed: {
      dialogVisible: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    methods: {
      async save() {
        this.loading = true;
        try {
          const key = this.configKey;
          const value = this.input;
          await this.$store.dispatch('Config/updateEntry', { key, value });
          this.$store.commit('showSnackbar', {
            text: this.$t('Config entry has been updated'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Config entry can not be updated'),
            color: 'error',
          });
        }
        this.loading = false;
        this.dialogVisible = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Edit configuration': 'Edycja konfiguracji',
          'Save': 'Zapisz',
          'Config entry can not be updated': 'Nie można zaktualizować wpisu konfiguracji',
          'Config entry has been updated': 'Wpis konfiguracji został zaktualizowany',
          'Group': 'Grupa',
          'Key': 'Klucz',
          'Value': 'Wartość',
        },
      },
    },
  };
</script>
