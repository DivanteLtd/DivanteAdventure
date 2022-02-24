<template>
  <v-card-text>
    <v-form v-model="formValid">
      <v-text-field class="required" v-model="item.name" :label="$t('Name')" :rules="[ rules.required ]" required/>
      <v-text-field class="required"
                    v-model="item.lastName"
                    :label="$t('Last name')"
                    :rules="[ rules.required ]"
                    required/>
      <v-text-field class="required"
                    v-model="item.manufacturer"
                    :label="$t('Manufacturer')"
                    :rules="[ rules.required ]"
                    required/>
      <v-text-field class="required"
                    v-model="item.model"
                    :label="$t('Model')"
                    :rules="[ rules.required ]"
                    required/>
      <v-text-field class="required"
                    v-model="item.serialNumber"
                    :label="$t('Serial number')"
                    :rules="[ rules.required ]"
                    required/>
      <v-checkbox v-model="attachEnglish" :label="$t('Attach English version of agreement')"/>
      <template v-if="item.contract === 'CoE'
        || item.contract === 'CLC LUMP SUM'
        || item.contract === 'CLC HOURLY'">
        <v-text-field class="required"
                      v-model="PESEL"
                      label="PESEL"
                      :rules="[ rules.required, rules.isValidPESEL ]"
                      required/>
      </template>
      <template v-else>
        <v-text-field class="required" v-model="company" :label="$t('Company')" :rules="[ rules.required ]" required/>
        <v-text-field class="required" v-model="headquarters"
                      :label="$t('Headquarters')" :rules="[ rules.required ]"
                      required/>
        <v-text-field class="required"
                      v-model="NIP"
                      label="NIP"
                      :rules="[ rules.required, rules.isValidNIP ]"
                      required/>
      </template>
      <v-btn color="success" :disabled="!formValid"
             @click="save" :loading="loading" block>
        {{ $t('Generate agreement') }}
      </v-btn>
    </v-form>
  </v-card-text>
</template>

<script>
  export default {
    name: 'HardwareForm',
    props: {
      item: { type: Object, required: true },
    },
    data() {
      return {
        company: '',
        headquarters: '',
        PESEL: '',
        NIP: '',
        formValid: false,
        loading: false,
        attachEnglish: false,
        rules: {
          required: val => !!val || this.$t('This field is required'),
          isValidPESEL: val => {
            const weight = [9, 7, 3, 1, 9, 7, 3, 1, 9, 7];
            let sum = 0;
            for(let i = 0; i < weight.length; i++) {
              sum += (parseInt(val.substring(i, i + 1), 10) * weight[i]);
            }
            sum %= 10;
            this.formValid = true;
            return sum === parseInt(val.substring(10, 11), 10) ? true : this.$t('PESEL is not valid');
          },
          isValidNIP: nip => {
            const weight = [6, 5, 7, 2, 3, 4, 5, 6, 7];
            let sum = 0;
            // eslint-disable-next-line no-useless-escape
            const controlNumber = parseInt(nip.replace(/[ \-]/gi, '').substring(9, 10));
            const weightCount = weight.length;
            for (let i = 0; i < weightCount; i++) {
              sum += (parseInt(nip.substr(i, 1)) * weight[i]);
            }
            this.formValid = true;
            return sum % 11 === controlNumber ? true : this.$t('NIP is not valid');
          },
        },
      };
    },
    methods: {
      async save() {
        this.loading = true;
        const details = { ...this.item };
        details.languages = this.attachEnglish ? [ 'pl', 'en' ] : [ 'pl' ];
        if (this.item.contract === 'CoE'
          || this.item.contract === 'CLC LUMP SUM'
          || this.item.contract === 'CLC HOURLY') {
          details.PESEL = this.PESEL.trim();
        } else {
          details.company = this.company;
          details.headquarters = this.headquarters;
          details.NIP = this.NIP.trim();
        }
        await this.$store.dispatch('Hardware/generateHardwareAgreement', details);
        this.loading = false;
        this.$store.commit(
          'showSnackbar',
          { text: this.$t('Hardware lending agreement have been generated'), color: 'green' }
        );
        this.$emit('close');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Company': 'Firma',
          'Headquarters': 'Siedziba',
          'Generate agreement': 'Generuj umowę',
          'This field is required': 'To pole jest wymagane',
          'PESEL is not valid': 'Numer PESEL jest błędny',
          'NIP is not valid': 'Numer NIP jest błędny',
          'Name': 'Imię',
          'Last name': 'Nazwisko',
          'Manufacturer': 'Producent',
          'Model': 'Model',
          'Serial number': 'Numer seryjny',
          'Hardware lending agreement have been generated': 'Umowa użyczenia sprzętu została wygenerowana',
          'Attach English version of agreement': 'Dołącz angielską wersję umowy',
        },
      },
    },
  };
</script>
