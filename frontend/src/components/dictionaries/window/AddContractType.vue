<template>
  <v-dialog v-model="dialogVisible" width="400" persistent>
    <v-card>
      <v-card-title class="headline">
        <span>{{ editFlag ? $t('Edit contract type') : $t('Add new contract type') }}</span>
      </v-card-title>
      <v-divider/>
      <v-card-text class="pt-0 pb-0">
        <v-row no-gutters wrap class="mb-2">
          <v-col cols="8" lg="6" sm="8">
            <v-text-field
              v-model="code"
              :value="code"
              :label="$t('Contract type code')"
              :rules="[rules.required]"
              required
            />
          </v-col>
        </v-row>
        <v-row no-gutters wrap class="mb-2">
          <v-col cols="8" lg="6" sm="8">
            <v-text-field
              v-model="name"
              :value="name"
              :label="$t('Contract type name')"
              :rules="[rules.required]"
              required
            />
          </v-col>
        </v-row>
        <v-row no-gutters wrap class="mb-2">
          <v-col cols="8" lg="6" sm="8">
            <v-text-field
              v-model="description"
              :value="description"
              :label="$t('Contract type description')"
              :rules="[rules.required]"
              required
            />
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue" :disabled="!checkRequired" @click="add()" text>
          {{ $t('Save') }}
        </v-btn>
        <v-btn text @click="dialogVisible = false">
          {{ $t('Close') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'AddContractType',
    data() {
      return {
        dialogVisible: false,
        editFlag: false,
        code: '',
        name: '',
        description: '',
        active: true,
        rules: {
          required: value => !!value || 'Required.',
        },
      };
    },
    computed: {
      checkRequired() {
        return (this.code !== '' && this.name !== '' && this.description !== '');
      },
    },
    methods: {
      async add() {
        const data = {
          name: this.name,
          description: this.description,
          code: this.code,
          active: this.active,
        };
        try {
          await this.$store.dispatch('ContractsType/create', data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Contract type has been added'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('ContractType cannot be added'),
            color: 'error',
          });
        }
        await this.$store.dispatch('ContractsType/load');
        this.dialogVisible = false;
        this.code = '';
        this.name = '';
        this.description = '';
      },
      show(data) {
        this.dialogVisible = true;
        this.editFlag = false;
        if (data) {
          this.editFlag = true;
        }
      },
    },
    created() {
      EventBus.$on(eventNames.addContractTYpe, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Edit contract type': 'Edytuj typ kontraktu',
          'Add new contract type': 'Stwórz nowy typ kontraktu',
          'Save': 'Zapisz',
          'Close': 'Anuluj',
          'Contract type code': 'Kod typu umowy',
          'Contract type name': 'Nazwa typu umowy',
          'Contract type description': 'Opis typu umowy',
          'Required.': 'Wymagane',
        },
      },
    },
  };
</script>
