<template>
  <v-dialog v-model="dialogVisible" max-width="600px" >
    <v-card>
      <v-app-bar color="transparent" class="headline" flat >
        <span>
          {{ $t('New status') }}
        </span>
        <v-spacer/>
        <v-btn icon @click="close"><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-card-text>
        <v-container grid-list-md class="checklist-table-employee pa-2">
          <v-text-field
            :rules="[rules.required]"
            v-model="possibleStatuses.label_pl"
            :label="this.$t('Label PL')">
          </v-text-field>
          <v-text-field
            :rules="[rules.required]"
            v-model="possibleStatuses.label_en"
            :label="this.$t('Label EN')">
          </v-text-field>
          <v-select :items="icons"
                    :rules="[rules.required]"
                    v-model="possibleStatuses.icon"
                    :menu-props="{ 'closeOnContentClick': true }"
                    :label="this.$t('Icon')">
            <v-list-item slot="item" slot-scope="props" @click="possibleStatuses.icon = props.item.value">
              <v-list-item-action>
                <v-icon :color="possibleStatuses.color || 'black'">
                  {{ props.item.value }}
                </v-icon>
              </v-list-item-action>
              <v-list-item-content>
                {{ props.item.text }}
              </v-list-item-content>
            </v-list-item>
          </v-select>
          <v-select :items="colors"
                    :rules="[rules.required]"
                    v-model="possibleStatuses.color"
                    :menu-props="{ 'closeOnContentClick': true }"
                    :label="this.$t('Color')">
            <v-list-item slot="item" slot-scope="props" @click="possibleStatuses.color = props.item.value">
              <v-list-item-action>
                <v-icon :color="props.item.value">
                  {{ possibleStatuses.icon || 'help_outline' }}
                </v-icon>
              </v-list-item-action>
              <v-list-item-content>
                {{ props.item.text }}
              </v-list-item-content>
            </v-list-item>
          </v-select>
          <v-checkbox
            v-model="possibleStatuses.default"
            :disabled="includesDefault"
            :label="this.$t('Default status')">
          </v-checkbox>
          <v-checkbox
            v-model="possibleStatuses.done"
            :label="this.$t('Done')">
          </v-checkbox>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn color="red" text @click="close">{{ $t('Cancel') }}</v-btn>
        <v-btn color="blue darken-1" :disabled="!formHasErrors" text @click="save">{{ $t('Save') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'StateDialog',
    props: {
      possibleStatuses: { type: Object, required: true, default: () => {} },
      value: { type: Boolean, required: true },
      includesDefault: { type: Boolean, required: true, default: false },
    },
    data() {
      return {
        colors: [{
          text: this.$t('Red'),
          value: 'red',
        }, {
          text: this.$t('Green'),
          value: 'green',
        }, {
          text: this.$t('Blue'),
          value: 'blue',
        }, {
          text: this.$t('Yellow'),
          value: 'yellow',
        }, {
          text: this.$t('Black'),
          value: 'black',
        }, {
          text: this.$t('Purple'),
          value: 'purple',
        }, {
          text: this.$t('Orange'),
          value: 'orange',
        }, {
          text: this.$t('Brown'),
          value: 'brown',
        }, {
          text: this.$t('Pink'),
          value: 'pink',
        }, {
          text: this.$t('Grey'),
          value: 'grey',
        }],
        icons: [{
          text: this.$t('Clock'),
          value: 'schedule',
        }, {
          text: this.$t('Exclamation mark'),
          value: 'error_outline',
        }, {
          text: this.$t('Done'),
          value: 'done',
        }, {
          text: this.$t('All done'),
          value: 'done_all',
        }, {
          text: this.$t('List'),
          value: 'assignment',
        }, {
          text: this.$t('Question mark'),
          value: 'help_outline',
        }, {
          text: this.$t('Person'),
          value: 'perm_identity',
        }, {
          text: this.$t('Chat'),
          value: 'forum',
        }, {
          text: this.$t('Letter'),
          value: 'mail_outline',
        }],
        rules: {
          required: value => !!value || this.$t('This field is required.'),
        },
      };
    },
    computed: {
      dialogVisible: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
          if (!val) {
            this.$emit('close');
          }
        },
      },
      formHasErrors() {
        return this.possibleStatuses.label_pl !== ''
          && this.possibleStatuses.label_en !== ''
          && this.possibleStatuses.icon !== ''
          && this.possibleStatuses.color !== '';
      },
    },
    methods: {
      close() {
        this.$emit('close');
      },
      save() {
        this.$emit('save', this.possibleStatuses);
        this.$emit('close');
      },
    },
    i18n: {
      messages: {
        pl: {
          'This field is required.': 'To pole jest wymagane.',
          'New status': 'Nowy status',
          'Label PL': 'Nazwa PL',
          'Label EN': 'Nazwa EN',
          'Color': 'Kolor',
          'Icon': 'Ikona',
          'Default status': 'Status domyślny',
          'Cancel': 'Anuluj',
          'Save': 'Zapisz',
          'Red': 'Czerwony',
          'Green': 'Zielony',
          'Blue': 'Niebieski',
          'Yellow': 'Żółty',
          'Black': 'Czarny',
          'Purple': 'Fioletowy',
          'Orange': 'Pomarańczowy',
          'Brown': 'Brązowy',
          'Pink': 'Różowy',
          'Grey': 'Szary',
          'Clock': 'Zegar',
          'Exclamation mark': 'Wykrzyknik',
          'Done': 'Gotowe',
          'All done': 'Wszystko gotowe',
          'List': 'Lista',
          'Question mark': 'Pytajnik',
          'Person': 'Osoba',
          'Chat': 'Rozmowa',
          'Letter': 'List',
        },
      },
    },
  };
</script>
