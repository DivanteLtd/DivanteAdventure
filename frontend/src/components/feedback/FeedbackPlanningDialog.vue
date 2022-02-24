<template>
  <v-dialog v-model="dialogVisible" width="700">
    <v-card>
      <v-card-title class="title">
        {{ $t('Plan feedback') }}
        <v-spacer/>
        <v-btn @click="dialogVisible = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <v-container>
          <v-row no-gutters wrap class="mb-2">
            <v-col cols="12" lg="8" sm="12">
              <v-date-picker :min="minimal"
                             v-model="date"
                             :locale="locale"
                             :first-day-of-week="$t('date.firstDayOfWeek')"/>
            </v-col>
            <v-col cols="12" lg="4" sm="12">
              <v-list>
                <v-list-item><b>{{ $t('Plan feedback:') }}</b></v-list-item>
                <v-list-item>
                  <v-btn color="primary"
                         @click="() => addMonths(1)"
                         block outlined>
                    {{ $t('In a month') }}
                  </v-btn>
                </v-list-item>
                <v-list-item>
                  <v-btn color="primary"
                         @click="() => addMonths(3)"
                         block outlined>
                    {{ $t('In a quarter') }}
                  </v-btn>
                </v-list-item>
                <v-list-item>
                  <v-btn color="primary"
                         @click="() => addMonths(6)"
                         block outlined>
                    {{ $t('In half a year') }}
                  </v-btn>
                </v-list-item>
                <v-list-item>
                  <v-btn color="primary"
                         @click="() => addMonths(12)"
                         block outlined>
                    {{ $t('In a year') }}
                  </v-btn>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn @click="save" color="primary" :loading="loading" block>
          {{ $t('Save') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import { getSuggestedLanguage } from '../../i18n/i18n';
  import { mapState } from 'vuex';

  export default {
    name: 'FeedbackPlanningDialog',
    props: {
      value: { type: Boolean, default: false },
      employee: { type: Object, required: true },
    },
    data() {
      return {
        date: moment().format('YYYY-MM-DD'),
        minimal: moment().format('YYYY-MM-DD'),
        locale: getSuggestedLanguage(),
        loading: false,
      };
    },
    computed: {
      ...mapState({
        currentUser: state => state.Employees.loggedEmployee,
      }),
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
      addMonths(count) {
        this.date = moment().add(count, 'months').format('YYYY-MM-DD');
      },
      async save() {
        this.loading = true;
        await this.$store.state.apiClient.feedback.planFeedback(this.employee.id, this.date);
        await this.$store.dispatch('Feedback/getMyStructure', this.currentUser.id);
        this.$emit('reload');
        this.dialogVisible = false;
        this.loading = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Plan feedback': 'Zaplanuj feedback',
          'Plan feedback:': 'Zaplanuj feedback:',
          'Save': 'Zapisz',
          'In a month': 'Za miesiąc',
          'In a quarter': 'Za kwartał',
          'In half a year': 'Za pół roku',
          'In a year': 'Za rok',
        },
      },
    },
  };
</script>
