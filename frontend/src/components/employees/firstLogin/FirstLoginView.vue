<template>
  <v-card>
    <v-card-title class="headline">{{ $t('Welcome!') }}</v-card-title>
    <v-divider/>
    <first-login-dialog-content v-model="employee" :loading="loadingData"/>
  </v-card>
</template>

<script>
  import FirstLoginDialogContent from './FirstLoginDialogContent';
  import { mapState } from 'vuex';

  const EMPTY_EMPLOYEE = {
    id: '',
    name: '',
    lastName: '',
    email: '',
    privatePhone: '',
    city: '',
    street: '',
    postalCode: '',
    country: '',
    gender: '',
    phone: '',
    workMode: '',
    hiredAt: '',
    tribe: '',
    position: '',
    level: '',
    contract: '',
    emergencyFirstName: '',
    emergencyLastName: '',
    emergencyPhone: '',
    pin: '',
    dateOfBirth: '',
    licencePlate: '',
    jobTimeValue: '',
    dataUpdate: '',
  };

  export default {
    name: 'FirstLoginView',
    components: { FirstLoginDialogContent },
    data() {
      return {
        employee: EMPTY_EMPLOYEE,
        loadingData: false,
      };
    },
    computed: {
      ...mapState({
        currentUser: state => state.Employees.loggedEmployee,
      }),
    },
    methods: {
      setEmployeeValue(val) {
        const {
          id,
          name,
          lastName,
          email,
          privatePhone,
          city,
          postalCode,
          street,
          country,
          gender,
          phone,
          workMode,
          hiredAt,
          tribe,
          position,
          level,
          contract,
          emergencyFirstName,
          emergencyLastName,
          emergencyPhone,
          pin,
          dateOfBirth,
          licencePlate,
          jobTimeValue,
          language,
          dataUpdate,
        } = val;
        this.employee = {
          id,
          name,
          lastName,
          email,
          privatePhone,
          city,
          postalCode,
          street,
          country,
          gender,
          phone,
          workMode,
          hiredAt,
          tribe,
          position,
          level,
          contract,
          emergencyFirstName,
          emergencyLastName,
          emergencyPhone,
          pin,
          dateOfBirth,
          licencePlate,
          jobTimeValue,
          language,
          dataUpdate,
        };
      },
    },
    async mounted() {
      this.setEmployeeValue(EMPTY_EMPLOYEE);
      this.loadingData = true;
      await this.$store.dispatch('Authorization/awaitFinishedLoading');
      await this.$store.dispatch('Config/loadContentConfig');
      this.setEmployeeValue(this.currentUser);
      if (this.employee.jobTimeValue > 1) {
        this.employee.jobTimeValue /= 28800;
      }
      await this.$store.dispatch('Tribes/loadTribes');
      this.loadingData = false;
    },
  };
</script>
