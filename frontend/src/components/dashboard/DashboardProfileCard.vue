<template>
  <dashboard-card :title="$t('Profile')" :loading="!loaded">
    <v-container>
      <v-row no-gutters v-if="employee.photo !== ''" wrap>
        <v-col md="6" cols="12" :class="{'photo-sm-wrapper': $vuetify.breakpoint.smAndDown}">
          <img v-if="employee.photo !== '' && employee.photo"
               class="photo"
               :src="employee.photo"
               :alt="
                 employee.name + ' ' + employee.lastName"/>
          <v-icon v-else large>perm_identity</v-icon>
        </v-col>
        <v-col md="6" cols="12">
          <div style="text-align: center" class="mt-4">
            <div>{{ employee.name }}</div>
            <div class="headline">{{ employee.lastName }}</div>
          </div>
        </v-col>
      </v-row>
      <v-row no-gutters v-else>
        <v-col md="12">
          <div style="text-align: center" class="mt-4">
            <div>{{ employee.name }}</div>
            <div class="headline">{{ employee.lastName }}</div>
          </div>
        </v-col>
      </v-row>
    </v-container>
    <v-list two-line class="pa-0">
      <template v-if="employee.phone">
        <v-divider inset/>
        <dashboard-profile-row icon="phone"
                               v-if="employee.phone"
                               :title="employee.phone"
                               :subtitle="$t('Business phone number')"/>
      </template>
      <template v-if="employee.email">
        <v-divider inset/>
        <dashboard-profile-row v-if="employee.email"
                               icon="mail"
                               :title="employee.email"
                               :subtitle="$t('E-mail address')"/>
      </template>
      <template v-if="employee.city">
        <v-divider inset/>
        <dashboard-profile-row icon="location_on"
                               :title="employee.city"
                               :subtitle="$t('City')"/>
      </template>
      <template v-if="employee.licencePlate">
        <v-divider inset/>
        <dashboard-profile-row icon="directions_car"
                               :title="employee.licencePlate"
                               :subtitle="$t('Car plate')"/>
      </template>
      <template v-if="employee.tribe || employee.position">
        <v-divider inset/>
        <dashboard-profile-row v-if="employee.tribe && employee.position"
                               icon="group_work"
                               :title="employee.tribe.name"
                               :subtitle="(employee.level || {}).name + ' ' + employee.position.name"/>
        <dashboard-profile-row v-else-if="employee.position"
                               icon="group_work"
                               :title="(employee.level || {}).name + ' ' + employee.position.name"
                               :subtitle="$t('Position')"/>
        <dashboard-profile-row v-else-if="employee.tribe"
                               icon="group_work"
                               :title="employee.tribe.name"
                               :subtitle="$t('Tribe')"/>
      </template>
      <template v-if="employee.freeToday">
        <v-divider inset/>
        <dashboard-profile-row icon="work_off" :title="$t('You have a free day today!')"/>
      </template>
    </v-list>
  </dashboard-card>
</template>

<script>
  import DashboardProfileRow from './DashboardProfileRow';
  import DashboardCard from './DashboardCard';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'DashboardProfileCard',
    components: { DashboardCard, DashboardProfileRow },
    props: {
      employee: { type: Object, default: () => ({}) },
      loaded: { type: Boolean, default: false },
    },
    methods: {
      openEdit() {
        EventBus.$emit(eventNames.showEmployeeWindow, this.employee);
      },
    },
    i18n: { messages: {
      pl: {
        'Profile': 'Profil',
        'Business phone number': 'Służbowy numer telefonu',
        'E-mail address': 'Adres e-mail',
        'Car plate': 'Numer rejestracyjny pojazdu',
        'City': 'Miasto',
        'Department': 'Dział',
        'Position': 'Stanowisko',
        'Tribe': 'Praktyka',
        'You have a free day today!': 'Masz dziś dzień wolny!',
      },
      en: {
        Tribe: 'Practice',
      },
    },
    },
  };
</script>

<style scoped>
  .photo {
    max-height: 130px;
    max-width: 130px;
  }
  .photo-sm-wrapper{
    display: flex;
    justify-content: center;
  }
</style>
