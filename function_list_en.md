# Adventure functional list

At the beginning I would like to point out that the entire system is based on the division of roles and functions (configuration in ```services.yml```).
Each role has different access rights to different system spaces and functionalities.

Role hierarchy (each subsequent one contains previous permissions): User -> Manager -> Tribe Master -> Administrator.

We also have additionally HR and Helpdesk functions which in the basis have the role of User and additionally their unique functionalities

1 \. Login:
- Users login via Google using earlier set domain
- Authorization takes place with the 4-digit pin set by the user when logging in to the site for the first time
    ![Alt text](docs/screenshots/login.jpg?raw=true)

2 \. Dashboard:
- User profile
- Lending agreements of hardware for signing (if exists)
- Newsletter - current company information
- Pending requests (only visible if the user has the role of manager or higher)
- Projects
- Useful links
- Checklists (visible only if the user is wearing checklist tasks)
- Website language selection icon - PL / ENG
- Remote work / trip calendar
- Logout in thumbnail of your photo
- Global search engine
    ![Alt text](docs/screenshots/dashboard.png?raw=true)

3 \. Company:
- Projects:
   * List of projects (basic information)
   * Division into active and archived projects
   * Search engine
   * Ability to add a new project
   * Ability to download a project report
   ![Alt text](docs/screenshots/projects.png?raw=true)

- Persons:
   * List of persons with basic information
   * Persons search (after various data, e.g. car registration)
   * Ability to download a list of all persons
   * Ability to download all persons' days off
   * Button to go to a person's days off / holidays module
   * Removal / Termination of cooperation with the person
   ![Alt text](docs/screenshots/persons.png?raw=true)

- Departments / Tribes:
   * List of tribes and departments with basic information
   * Division into tribes and departments
   * Search engine
   * Adding a new tribe / department
   ![Alt text](docs/screenshots/departments.png?raw=true)

4 \. Days off / holidays:
- Creating persons work periods:
   * Assigning days to be used in a given period both sick and days off / holidays
   * Assigning the duration of days off/ holidays / sick days
- Information about available and used days
- Requests submitted with the appropriate status (accepted / rejected etc.)
- Submission of new requests
- Cancellation of requests
- Deleting requests (for Administrator)
   ![Alt text](docs/screenshots/request.png?raw=true)

5 \. Working time records (Evidences):
- Complete the evidence form
- Adding invoice
- Sending evidence
- Archive of evidence sent
   ![Alt text](docs/screenshots/evidence.png?raw=true)
   
6 \. Summary of days off/holidays/sick days:
- Information table with a list of persons and their availability
   ![Alt text](docs/screenshots/workDaySummary.png?raw=true)

7 \. FAQ:
- List of questions and answers within the category
- Search engine
- Possibility to ask a new question
- Category management:
   * Adding a new category
   * Adding responsible persons to the category
   * Answer the questions asked
   ![Alt text](docs/screenshots/faq.png?raw=true)

8 \. Checklist:
> Detailed information about Checklist can be found in checklist_instruction_pl.pdf and checklist_instruction_en.pdf
- Checklist details
- Search checklist
- Removal of checklist
   ![Alt text](docs/screenshots/checklist.png?raw=true)

9 \. Feedback:
- Feedback details
- Split feedback into:
  * Received
  * Planned
  * Granted:
    * Edition
    * Delete
   ![Alt text](docs/screenshots/feedback.png?raw=true)

10 \. Agreements (GDPR, Marketing, ISO etc.):
- List of agreements to be accepted with division on type of contract
- The possibility of accepting agreements
- The ability to create new agreements
- Ability to add attachments
- Ability to download attachments for users
   ![Alt text](docs/screenshots/agreements.png?raw=true)

11 \. Acceptance List:
- Information table who accepted agreements
   ![Alt text](docs/screenshots/acceptationList.png?raw=true)

12 \. Request management:
- List of pending requests for approval
- List of planned requests
- List of archival requests
- Details of the requests:
   * Possibility to accept / reject
   * Option to add a comment to the requests
   ![Alt text](docs/screenshots/requestManagement.png?raw=true)

13 \. Hardware lending agreements:
- Integration with the Snipe.it system (thanks to this tool hardware contracts fall into the service)
- Split on agreements to generate and generated but not signed
- Generating hardware hire contracts via the generation form
- Option to delete contracts
- Search engine
   ![Alt text](docs/screenshots/hardware.png?raw=true)

14 \. Job structure management:
- List of created job structures and the jobs themselves
- Division of the view into structures and positions
- The ability to create a structure of positions
  * Possibility to create a station with the structure
- Ability to edit and delete data
   ![Alt text](docs/screenshots/jobStructureManagement.png?raw=true)

15 \. Configuration:
- Configuring access to external systems, e.g. Avaza, Gitlab, Snipe.it
- Possibility to add public holidays, so that in any place in the system these days will be treated as unavailable until, e.g. submitting an application for a day off / holiday
- Other: e.g. The ability to configure the integration in such a way that it informs about service errors on the technical channel on Slack.
    ![Alt text](docs/screenshots/configuration.png?raw=true)

16 \. Planner and statistics
> Information about the planner can be found in the files planer_functions_pl.md and planer_functions_en.md
- a tool for planning work in projects
    ![Alt text](docs/screenshots/planner.png?raw=true)

17 \. HR:
- List of persons:
   * Search engine
   * Ability to download a list of persons
   * Division of views into:
     * Active persons:
        * List of active persons with basic data
        * Ability to remove a person (termination of cooperation)
     * Potential persons:
         * List of potential persons with basic data
         * The ability to edit / delete person data
         * The ability to confirm or reject a potential person
     * Termination of cooperation:
         * List of persons who ended or will end cooperation
         * Option to delete / edit data
- Checklist templates:
  > Detailed information about Checklist can be found in checklist_instruction_pl.pdf and checklist_instruction_en.pdf
     * List of created checklist templates
     * Possibility of creating new checklist templates (separate and combined):
     * The ability to create new tasks as part of the checklist with the assignment of responsible persons
     * Ability to edit and delete checklist templates
     * Assigning a checklist to persons (owners and entities)
- Monthly reports, rotation statistics and department tribes:
  * View:
    * Number of persons in the company
    * Staff turnover between departments / tribes
    * Statistical data
    ![Alt text](docs/screenshots/hr.png?raw=true)

### Additional information:

1 \. Persons Profile:
- Persons personal data (basic)
- Role in the company (visible only to the Administrator)
- Projects (visible only in your profile)
- Working time records (visible only to the Administrator)
- Hardware (visible to the Administrator and User with the role of Helpdesk)
- Feedback (for superiors, leaders of a given person)
- Integration with Slack possible
    ![Alt text](docs/screenshots/personsProfile.png?raw=true)

2 \. Project details:
- Basic information about the project
- Persons in the project
- Criteria processed in the project
- Project editing
- Add / remove person in the project
- Deleting / archiving the project
    ![Alt text](docs/screenshots/projectDetails.png?raw=true)

3 \. Department/Tribe details:
- Department/Tribe persons + functions
- Department/Tribe posts
- Department/Tribe projects
- Department/Tribe removal
- Edition
    ![Alt text](docs/screenshots/tribeDetails.png?raw=true)
