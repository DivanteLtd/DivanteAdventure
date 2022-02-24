# Lista funkcjonalności Adventure

Na wstępie pragnę zaznaczyć, że cały system oparty jest na podziale ról i funkcji (konfiguracja w ```services.yml```).
Każda z ról posiada inne uprawnienia dostępowe do różnych przestrzeni systemu oraz funkcjonalności.

Hierarchia ról (każda następna, zawiera uprawnienia poprzedniej): User -> Manager -> Tribe Master -> Administrator.

Mamy też dodatkowo 2 funckje: HR oraz Helpdesk które w podstawie mają rolę User a dodatkowo swoje unikalnefunkcjonalności

1\. Logowanie:
- Logowanie użytkowników odbywa się poprzez serwis Google przy użyciu ustawionej domeny
- Autoryzacja odbywa się przez 4 cyfry pinu ustalone przez użytkownika przy okazji pierwszego logowania do serwisu
  ![Alt text](docs/screenshots/login.jpg?raw=true)
    
2\. Dashboard:
- Profil użytkownika
- Umowy użyczenia sprzętu do podpisania
- Newsletter - bieżące informacje firmowe
- Oczekujące wnioski ( widoczne tylko jeśli użytkownik ma rolę managera lub wyższą)
- Projekty
- Przydatne linki
- Checklisty (widoczna tylko jeśli użytkownik ma na sobie zadania związane z checklistami)
- Ikona wyboru języka serwisu - PL/ENG
- Kalendarz pracy zdalnej/podróży
- Możliwość wylogowania w miniaturce swojego zdjęcia
- Wyszukiwarka globalna
    ![Alt text](docs/screenshots/dashboard.png?raw=true)
    
3\. Firma:
- Projekty:
   * Lista projektów (podstawowe informację)
   * Podział na aktywne projekty oraz zarchiwizowane
   * Wyszukiwarka
   * Możliwość dodawania nowego projektu
   *  Możliwość pobranie raportu o projekcie
      ![Alt text](docs/screenshots/projects.png?raw=true)
      
- Osoby:
   * Lista osób wraz z podstawowymi informacjami
   * Wyszukiwarka osób (po różnych danych np. rejestracji auta)
   * Możliwość pobrania listy wszystkich osób
   * Możliwość pobrania dni wolnych wszystkich osób
   * Przejście do modułu dni wolnych / urlopów poszczególnej osoby
   * Usunięcie/Zakończenie współpracy z daną osobą
      ![Alt text](docs/screenshots/persons.png?raw=true)

- Działy / Plemiona:
   * Lista plemion oraz działów wraz z podstawowymi informacjami
   * Podział na plemiona i działy
   * Wyszukiwarka
   * Dodawanie nowego plemienia/działu
     ![Alt text](docs/screenshots/departments.png?raw=true)

4\. Dni wolne / urlopowe / niedostępności:
- Tworzenie okresów pracy osób:
   * Przypisywanie dni do wykorzystania w danym okresie zarówno chorobowych jak i dni wolnych/urlopowych/niedostępności
   * Przypisywanie okresu obowiązywania dni wolnych/urlopowych/chorobowych
- Informacja o przysługujących i wykorzystanych dniach
- Złożone wnioski z odpowiednim statusem (zaakceptowany/odrzucony etc.)
- Składanie nowych wniosków
- Rezygnacja z wniosków
- Usuwanie wniosków (dla administratorów)
   ![Alt text](docs/screenshots/request.png?raw=true)

5\. Ewidencje czasu pracy:
- Uzupełnianie formularza ewidencji
- Dodawanie faktury
- Wysyłanie ewidencji
- Archiwum ewidencji wysłanych
  ![Alt text](docs/screenshots/evidence.png?raw=true)
   
6\. Podsumowanie dni wolnych/urlopów/chorobowych:
- Tabela informacyjna wraz z listą osób oraz ich dostępnością
  ![Alt text](docs/screenshots/workDaySummary.png?raw=true)

7\. FAQ:
- Lista pytań i odpowiedzi w ramach kategorii
- Wyszukiwarka
- Możliwość zadania nowego pytania
- Zarządzanie kategoriami:
   * Dodanie nowej kategorii
   * Dodanie osób odpowiedzialnych do kategorii
   * Odpowiedź na zadane pytania
   ![Alt text](docs/screenshots/faq.png?raw=true)

8\. Checklisty:
> Szczegółowe informacje nt. Checklisty znajdziecie w pliku checklist_instruction_pl.pdf i checklist_instruction_en.pdf
- Szczegóły checklist
- Wyszukiwarka checklist
- Usunięcie checklist
  ![Alt text](docs/screenshots/checklist.png?raw=true)

9\. Feedback:
- Szczegóły Feedbacków
- Podział feedbacków na:
  * Otrzymane
  * Zaplanowane
  * Udzielone: 
    * Edycja
    * Usuwanie
    ![Alt text](docs/screenshots/feedback.png?raw=true)

10\. Zgody (RODO, Marketingowe, ISO etc.):
- Lista zgód do akceptacji wraz z podziałem na rodzaj umowy 
- Możliwość akceptacji zgód
- Możliwość tworzenia nowych zgód
- Możliwość dodawania załączników
- Możliwość pobieranie załączników dla użytkowników
  ![Alt text](docs/screenshots/agreements.png?raw=true)

11\. Lista akceptacji:
- Tabela informacyjna kto zaakceptował zgody
  ![Alt text](docs/screenshots/acceptationList.png?raw=true)

12\. Wnioski:
- Lista oczekujących wniosków na rozpatrzenie
- Lista zaplanowanych wniosków
- Lista archiwalnych wniosków
- Szczegóły wniosku:
   * Możliwość akceptacji/odrzucenia
   * Możliwość dodania komentarza do wniosku
   ![Alt text](docs/screenshots/requestManagement.png?raw=true)

13\. Umowy użyczenia sprzętu:
- Integracja z systemem Snipe.it (dzięki temu narzędziu umowy na sprzęt wpadają do serwisu)
- Podział na umowy do wygenerowania i wygenerowane ale nie podpisane
- Generowanie umów użyczenia sprzętu poprzez formularz generowania
- Możliwość usunięcia umów
- Wyszukiwarka do umów
  ![Alt text](docs/screenshots/hardware.png?raw=true)

14\. Zarządzanie stanowiskami:
- Lista utworzonych struktur stanowisk oraz samych stanowisk
- Podział widoku na struktury oraz stanowiska
- Możliwość tworzenia struktury stanowisk
  * Możliwość tworzenia stanowiska wraz z podpięciem pod strukturę
- Możliwość edycji i usuwania danych
  ![Alt text](docs/screenshots/jobStructureManagement.png?raw=true)

15\. Konfiguracja:
- Konfigurowanie dostępu do systemów zewnętrznych np.. Avaza, Gitlab, Snipe.it
- Możliwość dodania dni ustawowo wolnych, dzięki czemu w każdym miejscu w systemie te dni będą traktowane jako niedostępne do np. złożenia wniosku do urlopu
- Inne: np. Możliwość skonfigurowania integracji w taki sposób, aby informował o błędach serwisu na kanale technicznym na Slacku.
  ![Alt text](docs/screenshots/configuration.png?raw=true)

16\. Planer i statystyki 
> Informację o planerze znajdziecie w pliku planer_functions_pl.md oraz planer_functions_en.md
- narzędzie do planowania pracy w projektach
  ![Alt text](docs/screenshots/planner.png?raw=true)

17\. HR:
- Lista osób:
   * Wyszukiwarka osób
   * Możliwość pobrania listy osób
   * Podział widoków na:
       * Aktywne osoby:
         * Lista aktywnych osób wraz z podstawowymi danymi
         * Możliwość usunięcia osoby (zakończenia współpracy)
      * Potencjalne osoby:
         * Lista potencjalnych osób wraz z podstawowymi danymi
         * Możliwość edycji / usuwania danych osoby
         * Możliwość potwierdzenia lub odrzucenia potencjalnej osoby
      * Zakończenie współpracy:
         * Lista osób, którzy zakończyli lub zakończą współpracę
         * Możliwość usunięcia/edycja danych
- Szablony Checklist:
  > Szczegółowe informacje nt. Checklisty znajdziecie w pliku checklist_instruction_pl.pdf i checklist_instruction_en.pdf
     * Lista utworzonych templatek checklist
     * Możliwość tworzenie nowych templatek checklist (rozdzielnych i złączonych):
     * Możliwość tworzenie nowych zadań w ramach checklisty wraz z przypisaniem osób odpowiedzialnych osób
     * Możliwość edycji i usuwania templatek checklist
     * Przypisanie checklist do osób (właścicieli oraz podmiotów)
- Raporty miesięczne, statystyki rotacji oraz plemion działów:
  * Widok:
    * Ilości osób w firmie
    * Rotacji osób między działami/plemionami
    * Danych statystycznych
    ![Alt text](docs/screenshots/hr.png?raw=true)

### Dodatkowe informacje:

1\. Profil osoby:
- Dane personalne osoby (podstawowe)
- Rola w firmie (widoczna tylko i wyłącznie dla Administratora)
- Projekty (widoczne tylko w swoim profilu)
- Ewidencje czasu pracy (widoczna tylko i wyłącznie dla Administratora)
- Sprzęt (widoczny dla Administratora oraz Usera z rolą Helpdesk)
- Feedback (dla przełożonych, liderów danej osoby)
- Możliwość integracji ze Slackiem
  ![Alt text](docs/screenshots/personsProfile.png?raw=true)

2\. Szczegóły projektu:
- Podstawowe informację o projekcie
- Osoby w projekcie
- Kryteria przetwarzane w projekcie
- Edycja projektu
- Dodawanie/usuwanie osoby w projekcie
- Usuwanie/archiwizowanie projektu
  ![Alt text](docs/screenshots/projectDetails.png?raw=true)

3\. Szczegóły plemienia:
- Osoby w plemieniu + funkcje
- Stanowiska w plemieniu
- Projekty w plemieniu
- Usuwanie plemion
- Edycja
  ![Alt text](docs/screenshots/tribeDetails.png?raw=true)
