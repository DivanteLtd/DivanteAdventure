export default {
  global_search: {
    tribe_url: 'Strona plemienia',
    project_url: 'Strona projektu',
  },
  date: {
    firstDayOfWeek: '1',
    months: [
        'Styczeń',
        'Luty',
        'Marzec',
        'Kwiecień',
        'Maj',
        'Czerwiec',
        'Lipiec',
        'Sierpień',
        'Wrzesień',
        'Październik',
        'Listopad',
        'Grudzień',
    ],
    formats: {
      hourDuration: '{HH} godz.',
      shortDate: 'D MMMM YYYY',
      dateWithWeekDay: 'dddd, D MMMM YYYY',
    },
  },
  snackbar_redirect: {
    agreements: 'Przed rozpoczęciem korzystania z Adventure zaakceptuj wszystkie wymagane zgody',
    edit: 'Przed rozpoczęciem korzystania z Adventure uzupełnij wszystkie wymagane pola formularza',
  },
  menu: {
    group: {
      administration: 'Administracja',
      adventure: 'Adventure',
      agreements: 'Zgody',
      systemHR: 'HR',
      planner: 'Planer',
    },
    entry: {
      dashboard: 'Dashboard',
      feedback: 'Feedback',
      evidences: 'Ewidencje',
      overtime: 'Nadgodziny',
      personsList: 'Lista osób',
      agreements_general: 'Ogólne',
      agreements_marketing: 'Marketingowe',
      agreements_acceptations: 'Lista akceptacji',
      firm: 'Firma',
      firm_departments: 'Działy',
      firm_employees: 'Osoby',
      firm_projects: 'Projekty',
      firm_tribes: 'Praktyki i działy',
      free_days: 'Dni wolne',
      unavailability_days: 'Dni niedostępności',
      leave_days: 'Dni urlopowe',
      free_days_dashboard: 'Podsumowanie dni wolnych',
      general: 'Ogólne',
      checklistTemplates: 'Szablony checklist',
      requests: 'Wnioski',
      scheduler: 'Planer',
      rotationStats: 'Statystyki rotacji',
      positions: 'Zarządzanie stanowiskami',
      monthlyReport: 'Raport miesięczny',
      tribeStats: 'Statystyki praktyk',
      checklists: 'Checklisty',
      faq: 'FAQ',
      scheduler_stats: 'Planer - statystyki',
      hardware_agreements: 'Umowy użyczenia sprzętu',
      system_config: 'Konfiguracja',
      system_dictionaries: 'Słowniki',
    },
  },
  config: {
    groups: {
      slack: 'Slack',
      gitlab: 'Gitlab',
      snipe_it: 'Snipe IT',
      avaza: 'Avaza',
      tribe: 'Połączone praktyki',
      content: 'Treści',
      payroll: 'Płace i kadry',
      evidence: 'Ewidencja',
    },
    entries: {
      slack: {
        client_id: 'ID klienta',
        client_secret: 'Sekret klienta',
        redirect_uri: 'Adres przekierowania',
        admin_id: 'ID integracji admina',
        admin_access_token: 'Token dostępu integracji admina',
        admin_status: 'Status integracji admina',
      },
      gitlab: {
        instance_url: 'Adres URL instancji',
        token: 'Token dostępu',
      },
      snipe_it: {
        instance_url: 'Adres URL instancji',
        token: 'Token dostępu',
      },
      avaza: {
        token: 'Token dostępu',
        sick_leave_project_id: 'ID projektu zwolnienia chorobowego',
        sick_leave_category_id: 'ID kategorii zwolnienia chorobowego',
        free_day_project_id: 'ID projektu dnia wolnego',
        free_day_category_id: 'ID kategorii dnia wolnego',
      },
      tribe: {
        connected1_id: 'Połączone plemię 1 ID',
        connected2_id: 'Połączone plemię 2 ID',
      },
      content: {
        agreement_general_en: 'Zgody - ogólne info EN',
        agreement_ohs_en: 'Zgody - BHP info EN',
        agreement_gdpr_en: 'Zgody - RODO info EN',
        agreement_iso_en: 'Zgody - ISO info EN',
        agreement_marketing_main_en: 'Zgody - Marketingowe info główne EN',
        agreement_marketing_en: 'Zgody - Marketingowe info EN',
        iso_link_en: 'Link do Zarządzania Systemem ISO EN',
        agreement_general_pl: 'Zgody - ogólne info PL',
        agreement_ohs_pl: 'Zgody - BHP info PL',
        agreement_gdpr_pl: 'Zgody - RODO info PL',
        agreement_iso_pl: 'Zgody - ISO info PL',
        agreement_marketing_main_pl: 'Zgody - Marketingowe info główne PL',
        agreement_marketing_pl: 'Zgody - Marketingowe info PL',
        iso_link_pl: 'Link do Zarządzania Systemem ISO PL',
        email_domain: 'Domena utworzonego adresu e-mail pracownika',
        work_mode_link: 'Tryb pracy - link do Confluence',
      },
      payroll: {
        email_for_b2b_pl: 'Adres (adresy) email do osoby (osób) zajmujących się pracownikami (B2B) mieszkającymi na terenie Polski. Możliwość wprowadzenia kilka adresów po przecinku',
        email_for_b2b_de: 'Adres (adresy) email do osoby (osób) zajmujących się pracownikami (B2B) mieszkającymi na terenie Niemczech. Możliwość wprowadzenia kilka adresów po przecinku',
        email_for_not_b2b_pl: 'Adres (adresy) email do osoby (osób) zajmujących się pracownikami (inne niż B2B) mieszkającymi na terenie Polski. Możliwość wprowadzenia kilka adresów po przecinku',
        email_for_not_b2b_de: 'Adres (adresy) email do osoby (osób) zajmujących się pracownikami (inne niż B2B) mieszkającymi na terenie Niemczech. Możliwość wprowadzenia kilka adresów po przecinku',
        email_for_coe_pl: 'Adres (adresy) email do osoby (osób) zajmujących się pracownikami (Umowa o pracę) mieszkającymi na terenie Polski. Możliwość wprowadzenia kilka adresów po przecinku',
        email_for_coe_de: 'Adres (adresy) email do osoby (osób) zajmujących się pracownikami (Umowa o pracę) mieszkającymi na terenie Niemczech. Możliwość wprowadzenia kilka adresów po przecinku',
      },
      evidence: {
        email_for_b2b_pl: 'Adresy email do osób zajmujących się fakturami kosztowymi pracowników (B2B) mieszkającymi na terenie Polski. Możliwość wprowadzenia kilka adresów po przecinku',
        email_for_b2b_de: 'Adresy email do osób zajmujących się fakturami kosztowymi pracowników (B2B) mieszkającymi na terenie Niemczech. Możliwość wprowadzenia kilka adresów po przecinku',
        email_for_not_b2b_pl: 'Adresy email do osób zajmujących się fakturami kosztowymi pracowników (inne niż B2B) mieszkającymi na terenie Polski. Możliwość wprowadzenia kilka adresów po przecinku',
        email_for_not_b2b_de: 'Adresy email do osób zajmujących się fakturami kosztowymi pracowników (inne niż B2B) mieszkającymi na terenie Niemczech. Możliwość wprowadzenia kilka adresów po przecinku',
      },
    },
    freeDays: {
      christian: {
        easter: 'Wielkanoc (chrześcijański)',
        easter_monday: 'Poniedziałek Wielkanocny (chrześcijański)',
        corpus_christi: 'Boże Ciało (chrześcijański)',
        pentecost: 'Zielone Świątki (chrześcijański)',
        good_friday: 'Wielki Piątek (chrześcijański)',
        ascension_day: 'Wniebowstąpienie (chrześcijański)',
        pentecost_monday: 'Drugi dzień Zielonych Świątek (chrześcijański)',
      },
      us: {
        martin_luther_king_jr_birthday: 'Dzień Martina Luthera Kinga Jr. (USA)',
        george_washington_birthday: 'Dzień Jerzego Waszyngtona (USA)',
        memorial_day: 'Memorial Day (USA)',
        labor_day: 'Święto Pracy (USA)',
        columbus_day: 'Dzień Kolumba (USA)',
        thanksgiving_day: 'Dzień Dziękczynienia (USA)',
      },
    },
  },
  notifications: {
    'You have been added to tribe': 'Zostałeś dodany do plemienia',
    'Your request is accepted ID:': 'Twój wniosek jest zaakceptowany ID:',
    'Your resignation request is accepted ID:': 'Twój wniosek o rezygnacje jest zaakceptowany ID:',
    'Your resignation request is rejected ID:': 'Twój wniosek o rezygnacje zastał odrzucony ID:',
    'Your request is rejected ID:': 'Twój wniosek został odrzucony ID:',
    'You have outstanding request to manage for': 'Masz oczekujący wniosek do rozpatrzenia dla',
    'You have outstanding planned request to accept for:': 'Masz oczekujący zaplanowany wniosek do akceptacji dla',
    'You have outstanding evidence to manage for': 'Masz oczekującą ewidencję do rozpatrzenia dla',
    'Period has been edited in your project': 'Został zmieniony Twój okres w projekcie',
    'Person has resign from resignation request ID:': 'Osoba anulowała swoją rezygnację z wniosku ID:',
    'Person has resign from request ID:': 'Osoba zrezygnowała z wniosku ID:',
    'Person has resign from planned request ID:': 'Osoba zrezygnowała z zaplanowanego wniosku ID:',
    'You have outstanding resign request to manage for': 'Masz oczekujący wniosek o rezygnacje do rozpatrzenia dla',
    'All yours periods have been deleted from project': 'Usunięto wszystkie Twoje okresy z projektu',
    'One period have been deleted from your project': 'Usunięto Ci okres w projekcie',
    'You have been deleted from tribe': 'Zostałeś usunięty z plemienia',
    'You have outstanding hardware lending agreement to generate for': 'Masz oczekującą umowę użyczenia sprzętu do wygenerowania dla',
    'You have outstanding hardware lending agreement to sign for': 'Masz oczekującą umowę użyczenia sprzętu do podpisania dla',
    'You have been given a new period in the project': 'Przydzielono Ci nowy okres w projekcie',
    'Logout successfully': 'Wylogowano poprawnie',
  },
};