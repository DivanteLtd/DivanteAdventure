<template>
  <tr>
    <td class="project-name">
      <v-autocomplete v-model="project"
                      :items="availableProjects"
                      :item-text="item => item.name + ' [' + item.code + ']'"
                      :no-data-text="$t('No projects available')"
                      return-object>
        <div class="body-1" slot="item" slot-scope="props">
          {{ props.item.name }} [{{ props.item.code }}]
        </div>
      </v-autocomplete>
    </td>
    <td>
      <v-container>
        <v-row no-gutters
               :class="{'working-hours-xs': $vuetify.breakpoint.xsOnly,
                        'working-hours-sm': $vuetify.breakpoint.smAndUp}"
               wrap>
          <v-spacer cols="1"/>
          <v-col cols="5">
            <v-text-field v-model="hours"
                          :placeholder="$t('HH')"
                          type="number"
                          min="0"
                          max="24"
                          :rules="checkRules.hour"/>
          </v-col>
          <v-spacer cols="1"/>
          <v-col cols="5">
            <v-text-field v-model="minutes"
                          :placeholder="$t('MM')"
                          type="number"
                          min="0"
                          max="59"
                          :rules="checkRules.minute"/>
          </v-col>
        </v-row>
      </v-container>
    </td>
    <td>
      <v-select v-model="rate" :items="['100%', '150%', '200%']"/>
    </td>
    <td>
      <div class="date-row">
        <v-menu
          v-model="dataPicker"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="days"
              prepend-icon="mdi-calendar"
              readonly
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="days"
            @input="dataPicker = false"
          ></v-date-picker>
        </v-menu>
        <v-tooltip right top>
          <template v-slot:activator="{ on }">
            <v-avatar v-on="on">
              <v-icon >help_outline</v-icon>
            </v-avatar>
          </template>
          {{ $t('This field accept only numbers and special characters') }}
        </v-tooltip>
      </div>
    </td>
    <td>
      <v-container class="d-flex">
        <v-btn icon small @click="emitAdd">
          <v-icon color="#00AA00">add</v-icon>
        </v-btn>
        <v-btn icon small @click="emitSubtract" :disabled="itemsCount < 2">
          <v-icon color="red">remove</v-icon>
        </v-btn>
      </v-container>
    </td>
  </tr>
</template>

<script>
  import { mapState } from 'vuex';

  export default {
    name: 'EvidenceOvertimeRow',
    props: {
      item: { type: Object, required: true },
      itemsCount: { type: Number, default: 0 },
    },
    data() { return {
      dataPicker: false,
      rules: {
        text: [
          v => !!v || this.$t('This field is required'),
        ],
        date: [
          v => !!v || this.$t('This field is required'),
          v => /^[0-9.-]*$/.test(v) || this.$t('This field accept only numbers and special characters'),
        ],
        hour: [
          v => !!v || v === 0 || this.$t('This field is required'),
          v => v >= 0 || this.$t('This value cannot be lesser than zero'),
          v => v < 25 || this.$t('This value cannot be greater than 24'),
        ],
        minute: [
          v => !!v || v === 0 || this.$t('This field is required'),
          v => v >= 0 || this.$t('This value cannot be lesser than zero'),
          v => v < 60 || this.$t('This value cannot be greater than 59'),
        ],
      },
      rulesMobile: {
        text: [
          v => !!v || this.$t(' '),
        ],
        date: [
          v => !!v || this.$t(' '),
          v => /^[0-9.-]*$/.test(v) || this.$t(' '),
        ],
        hour: [
          v => !!v || v > 0 || this.$t(' '),
          v => v >= 0 || this.$t(' '),
        ],
        minute: [
          v => !!v || v === 0 || this.$t(' '),
          v => v >= 0 || this.$t(' '),
          v => v < 60 || this.$t(' '),
        ],
      },
    };},
    computed: {
      ...mapState({
        projects: state => state.Projects.projects,
      }),
      availableProjects() {
        return this.projects.filter(project => project.hasOwnProperty('code') && project.code !== '?');
      },
      checkRules() {
        return this.$vuetify.breakpoint.smAndUp ? this.rules : this.rulesMobile;
      },
      project: {
        get() {
          return this.availableProjects.filter(project => project.code === this.code)[0];
        },
        set(val) {
          const name = val.name;
          const code = val.code;
          this.emitUpdate({ name, code });
        },
      },
      hours: {
        get() {
          return this.item.hours;
        },
        set(hours) {
          this.emitUpdate({ hours });
        },
      },
      minutes: {
        get() {
          return this.item.minutes;
        },
        set(minutes) {
          this.emitUpdate({ minutes });
        },
      },
      rate: {
        get() {
          return this.item.rate || '100%';
        },
        set(rate) {
          this.emitUpdate({ rate });
        },
      },
      days: {
        get() {
          return this.item.days;
        },
        set(days) {
          this.emitUpdate({ days });
        },
      },
    },
    methods: {
      isNumber(evt) {
        evt = (evt) || window.event;
        const ASCII = {
          ZERO: 48,
          NINE: 57,
          DOT: 45,
          COMMA: 46,
        };
        const charCode = (evt.which) ? evt.which : evt.keyCode;
        if ((charCode >= ASCII.ZERO && charCode <= ASCII.NINE)
          || (charCode >= ASCII.DOT && charCode <= ASCII.COMMA)) {
          return true;
        } else {
          evt.preventDefault();
        }
        return false;
      },
      emitUpdate(values) {
        const item = {
          name: this.item.name,
          code: this.item.code,
          hours: this.item.hours,
          minutes: this.item.minutes,
          rate: this.item.rate,
          days: this.item.days,
        };
        for (const parameter in values) {
          if (values.hasOwnProperty(parameter)) {
            item[parameter] = values[parameter];
          }
        }
        this.$emit('update:item', item);
      },
      emitAdd() {
        this.$emit('add-row');
      },
      emitSubtract() {
        this.$emit('remove-row');
      },
    },
    i18n: { messages: {
      pl: {
        'i.e. Adventure': 'Np. Adventure',
        'i.e. DDA001': 'Np. DDA001',
        'This field is required': 'To pole jest wymagane',
        'This value cannot be lesser than zero': 'Ta wartość nie może być mniejsza od zera',
        'This value cannot be greater than 59': 'Ta wartość nie może być większa niż 59',
        'No projects available': 'Brak dostępnych projektów',
        'This field accept only numbers and special characters': 'To pole akceptuje tylko cyfry i znaki specjalne',
        'This value cannot be greater than 24': 'Ta wartość nie może być większa niż 24',
      },
    } },
  };
</script>

<style scoped>
  .working-hours-sm{
    min-width: 10rem;
  }
  .working-hours-xs{
    min-width: 5rem;
  }
  .project-name{
    min-width: 8.5rem;
  }
  td{
    padding: 0 1rem !important;
  }
  .date-row{
    display: flex;
    align-items: center;
  }
</style>
