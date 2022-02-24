<template>
  <v-dialog width="400" v-model="modelWrapper" persistent>
    <v-card>
      <v-card-title class="title">
        {{ $t('Enter password') }}
        <v-spacer/>
        <v-btn @click="modelWrapper = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-card-text class="pb-0">
        <v-form v-model="formValid">
          <v-text-field :label="$t('Password')"
                        v-model="password"
                        type="password"
                        prepend-inner-icon="vpn_key"
                        full-width solo/>
        </v-form>
      </v-card-text>
      <v-card-actions class="d-flex justify-center">
        <v-btn @click="save" color="primary" :disabled="!formValid" text>{{ actionName }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'PasswordDialog',
    props: {
      value: { type: Boolean, required: true },
      actionName: { type: String, required: true },
    },
    data() {
      return {
        password: '',
        formValid: false,
      };
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
      save() {
        this.modelWrapper = false;
        this.$emit('password-entered', this.password);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Password': 'Hasło',
          'Enter password': 'Podaj hasło',
        },
      },
    },
  };
</script>
