<template>
  <div style="width: 100%">
    <v-text-field
      v-model="namePl"
      class="px-2 py-1 mt-2"
      :label="$t('Checklist template name PL')"
      :rules="[rules.required]"
      prepend-icon="notes"
      required
    ></v-text-field>
    <v-text-field
      v-model="nameEn"
      :rules="[rules.required]"
      class="px-2 py-1"
      :label="$t('Checklist template name EN')"
      prepend-icon="notes"
      required
    ></v-text-field>
    <div class="caption">{{ $t('caption-owner') }}</div>
    <v-radio-group :disabled="edit" v-model="type" row class="px-2">
      <v-radio class="mb-3"
               :value="ChecklistType.united"
               :label="$t('United - where the responsibility for the entire list has one person')"/>
      <v-radio :value="ChecklistType.distributed"
               :label="$t('Distributed - where each task has its responsible person.')"/>
    </v-radio-group>
    <div class="buttons">
      <v-spacer/>
      <v-btn color="red"
             text
             @click="$emit('close')">
        {{ $t('Cancel') }}
      </v-btn>
      <v-btn color="primary"
             text
             :disabled="!formHasErrors"
             @click="emitSaveChecklist">
        {{ $t('Next') }}
      </v-btn>
    </div>
  </div>
</template>

<script>
  import { ChecklistType } from '../../../../util/checklists';

  export default {
    name: 'ChecklistTemplatesWindowContent',
    props: {
      employees: { required: true, type: Array },
      edit: { required: true, type: Boolean },
      checklistTemplate: { type: Object, default: () => {} },
    },
    data() {
      return {
        ChecklistType,
        id: '',
        namePl: '',
        nameEn: '',
        employee: null,
        type: ChecklistType.united,
        rules: {
          required: value => !!value || 'Required.',
        },
      };
    },
    computed: {
      formHasErrors() {
        return this.namePl !== '' && this.nameEn !== '';
      },
    },
    methods: {
      async initialize() {
        if(this.employees.length === 0) {
          await this.$store.dispatch('Employees/loadEmployees');
        }
      },
      emitSaveChecklist() {
        if(this.nameEn !== this.checklistTemplate.nameEn || this.namePl !== this.checklistTemplate.namePl) {
          const data = {
            id: this.id,
            namePl: this.namePl,
            nameEn: this.nameEn,
            type: this.type,
          };
          this.$emit('saveChecklist', data, this.type === ChecklistType.united);
        }
        else{
          this.$emit('goToNextStep', this.type === ChecklistType.united);
        }
      },
      setChecklistTemplate() {
        this.id = this.checklistTemplate.id;
        this.namePl = this.checklistTemplate.namePl;
        this.nameEn = this.checklistTemplate.nameEn;
        this.type = this.checklistTemplate.type;
      },
    },
    async mounted() {
      if(this.edit) {
        this.setChecklistTemplate();
      }
      await this.initialize();
    },
    i18n: {
      messages: {
        pl: {
          'Cancel': 'Anuluj',
          'Checklist template name PL': 'Nazwa szablonu checklisty PL',
          'Checklist template name EN': 'Nazwa szablonu checklisty EN',
          'Distributed': 'Rozdzielony',
          'United': 'Złączony',
          'United - where the responsibility for the entire list has one person': 'Złączony - gdzie odpowiedzialność za całą listę spoczywa na pojedynczej osobie',
          'Distributed - where each task has its responsible person.': 'Rozdzielony - gdzie każde zadanie ma swoją osobę odpowiedzialną.',
          'Next': 'Dalej',
          'caption-owner': 'Nazwa w szablonie może zawierać w sobie zmienne “%OWNER%” i “%SUBJECT%”, w które będą wprowadzone imię i nazwisko odpowiednio właściciela i podmiotu listy',
        },
        en: {
          'caption-owner': 'The name in the template may contain the variables %OWNER%" and %SUBJECT%, in which the name of the owner and the entity of the list respectively will be entered.',
        },
      },
    },
  };
</script>

<style scoped>
  .caption{
    padding: 0 10px 10px 40px;
  }
  .buttons {
    display: flex;
  }
</style>
