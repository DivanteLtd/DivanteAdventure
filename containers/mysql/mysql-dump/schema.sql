create table if not exists agreement
(
    id            int auto_increment
        primary key,
    name_pl       varchar(50) not null,
    descriptionPl text        not null,
    required      tinyint(1)  not null,
    priority      int         not null,
    created_at    datetime    not null,
    updated_at    datetime    not null,
    type          int         not null,
    descriptionEn text        not null,
    name_en       varchar(50) not null,
    contract_ids  longtext    not null comment '(DC2Type:simple_array)'
)
    charset = utf8;

create table if not exists attachment
(
    id   int auto_increment
        primary key,
    path varchar(100) not null,
    name varchar(100) not null,
    constraint UNIQ_795FD9BB5E237E06
        unique (name),
    constraint UNIQ_795FD9BBB548B0F
        unique (path)
)
    collate = utf8_unicode_ci;

create table if not exists agreement_attachment
(
    agreement_id  int not null,
    attachment_id int not null,
    primary key (agreement_id, attachment_id),
    constraint FK_786F779824890B2B
        foreign key (agreement_id) references agreement (id)
            on delete cascade,
    constraint FK_786F7798464E68B
        foreign key (attachment_id) references attachment (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_786F779824890B2B
    on agreement_attachment (agreement_id);

create index IDX_786F7798464E68B
    on agreement_attachment (attachment_id);

create table if not exists agreement_attachment_download_token
(
    id            int auto_increment
        primary key,
    attachment_id int         null,
    created_at    datetime    not null,
    token         varchar(64) not null,
    constraint FK_ED3F329F464E68B
        foreign key (attachment_id) references attachment (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_ED3F329F464E68B
    on agreement_attachment_download_token (attachment_id);

create table if not exists checklist_template
(
    id         int auto_increment
        primary key,
    type       smallint     not null,
    name_pl    varchar(255) not null,
    name_en    varchar(255) not null,
    created_at datetime     not null,
    updated_at datetime     not null
)
    collate = utf8_unicode_ci;

create table if not exists company
(
    id           int auto_increment
        primary key,
    name         varchar(255) not null,
    address      mediumtext   not null,
    email_domain mediumtext   not null,
    vat_id       varchar(20)  not null
)
    collate = utf8_unicode_ci;

create table if not exists contract_type
(
    id          int auto_increment
        primary key,
    code        varchar(10)  not null,
    name        varchar(50)  not null,
    description varchar(255) not null,
    active      tinyint(1)   not null,
    created_at  datetime     not null,
    updated_at  datetime     not null
)
    collate = utf8_unicode_ci;

create table if not exists data_processing_criterium
(
    id      int auto_increment
        primary key,
    name_pl varchar(80) not null,
    name_en varchar(80) not null,
    constraint UNIQ_1AA881CE5E237E06
        unique (name_pl)
)
    collate = utf8_unicode_ci;

create table if not exists employee_work_location
(
    id          int auto_increment
        primary key,
    employee_id int  not null,
    type        int  not null,
    date        date not null
)
    collate = utf8_unicode_ci;

create table if not exists faq_category
(
    id         int auto_increment
        primary key,
    name_pl    varchar(50) not null,
    name_en    varchar(50) not null,
    created_at datetime    not null,
    updated_at datetime    not null
)
    collate = utf8_unicode_ci;

create table if not exists gitlab_project
(
    id          int auto_increment
        primary key,
    gitlab_id   int          not null,
    gitlab_type int          not null,
    name        varchar(255) not null,
    created_at  datetime     not null,
    updated_at  datetime     not null
)
    collate = utf8_unicode_ci;

create table if not exists leveling_strategy
(
    id         int auto_increment
        primary key,
    created_at datetime    not null,
    updated_at datetime    not null,
    name       varchar(50) not null
)
    collate = utf8_unicode_ci;

create table if not exists level
(
    id          int auto_increment
        primary key,
    strategy_id int           not null,
    created_at  datetime      not null,
    updated_at  datetime      not null,
    name        varchar(50)   not null,
    priority    int default 0 not null,
    constraint FK_9AEACC13D5CAD932
        foreign key (strategy_id) references leveling_strategy (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_9AEACC13D5CAD932
    on level (strategy_id);

create table if not exists migration_versions
(
    version varchar(255) not null
        primary key
)
    collate = utf8_unicode_ci;

create table if not exists notification
(
    id          int auto_increment
        primary key,
    employee_id int          not null,
    description varchar(150) not null,
    subject     varchar(150) null,
    path        varchar(50)  null,
    bold        int          null
)
    collate = utf8_unicode_ci;

create table if not exists position
(
    id          int auto_increment
        primary key,
    strategy_id int         not null,
    created_at  datetime    not null,
    updated_at  datetime    not null,
    name        varchar(50) not null,
    constraint FK_462CE4F5D5CAD932
        foreign key (strategy_id) references leveling_strategy (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create table if not exists employee
(
    id                       int auto_increment
        primary key,
    email                    varchar(254)             not null,
    name                     varchar(150)             not null,
    last_name                varchar(150)             not null,
    photo                    varchar(255)             null,
    phone                    varchar(50)              null,
    car                      varchar(50)              null,
    city                     varchar(50)              null,
    work_mode                int                      null,
    hired_at                 date                     null,
    hired_to                 date                     null,
    contract_id              int                      null,
    created_at               datetime                 not null,
    updated_at               datetime                 not null,
    private_phone            varchar(50)              null,
    job_time_value           decimal                  not null,
    tribe_id                 int                      null,
    gender                   int                      null,
    emergency_first_name     varchar(255)             null,
    emergency_last_name      varchar(255)             null,
    emergency_address        varchar(255)             null,
    emergency_phone          varchar(255)             null,
    position_id              int                      null,
    level_id                 int                      null,
    pin                      varchar(255)             null,
    pin_locked               tinyint(1)               not null,
    first_fail_time          datetime                 null,
    fail_count               int                      not null,
    date_of_birth            date                     null,
    child_care               int                      not null,
    gitlab_id                int                      null,
    slack_id                 longtext                 null,
    slack_access_token       longtext                 null,
    slack_status             smallint                 not null,
    language                 varchar(2)               null,
    last_slack_message_sent  datetime                 null,
    nip                      varchar(15)              null,
    firm_name                varchar(255)             null,
    firm_address             varchar(255)             null,
    avaza_id                 varchar(16)              null,
    postal_code              varchar(50)              null,
    street                   varchar(100)             null,
    country                  varchar(100)             null,
    data_update              datetime                 null,
    tech_tribe_leader        tinyint(1)               not null,
    company_branch           varchar(2) default 'PL'  not null,
    employee_code            varchar(100)             null,
    token_expiration_seconds int        default 86400 not null,
    superior_id              int                      null,
    finance_code             varchar(255)             null,
    student                  tinyint(1)               null,
    tax_deductible_costs     int        default 0     null,
    work_street              varchar(255)             null,
    work_city                varchar(255)             null,
    work_postal_code         varchar(255)             null,
    work_country             varchar(255)             null,
    shoe_size                varchar(255)             null,
    sweatshirt_size          varchar(255)             null,
    shirt_size               varchar(255)             null,
    date_reset_pin           date                     null,
    outsource_sub_type       varchar(255)             null,
    constraint email
        unique (email),
    constraint FK_5D9F75A15FB14BA7
        foreign key (level_id) references level (id)
            on delete set null,
    constraint FK_5D9F75A1DD842E46
        foreign key (position_id) references position (id)
            on delete set null
)
    charset = utf8;

create table if not exists checklist
(
    id          int auto_increment
        primary key,
    owner_id    int          null,
    subject_id  int          not null,
    type        smallint     not null,
    name_pl     varchar(255) not null,
    name_en     varchar(255) not null,
    started_at  datetime     not null,
    finished_at datetime     null,
    created_at  datetime     not null,
    updated_at  datetime     not null,
    hidden      tinyint(1)   not null,
    due_date    datetime     not null,
    constraint FK_5C696D2F23EDC87
        foreign key (subject_id) references employee (id)
            on delete cascade,
    constraint FK_5C696D2F7E3C61F9
        foreign key (owner_id) references employee (id)
            on delete set null
)
    collate = utf8_unicode_ci;

create index IDX_5C696D2F23EDC87
    on checklist (subject_id);

create index IDX_5C696D2F7E3C61F9
    on checklist (owner_id);

create table if not exists checklist_owner
(
    checklist_id int not null,
    employee_id  int not null,
    primary key (checklist_id, employee_id),
    constraint FK_F18861A28C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade,
    constraint FK_F18861A2B16D08A7
        foreign key (checklist_id) references checklist (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_F18861A28C03F15C
    on checklist_owner (employee_id);

create index IDX_F18861A2B16D08A7
    on checklist_owner (checklist_id);

create table if not exists checklist_question
(
    id                int auto_increment
        primary key,
    responsible_id    int          null,
    checklist_id      int          not null,
    name_pl           varchar(255) not null,
    name_en           varchar(255) not null,
    description_pl    varchar(255) not null,
    description_en    varchar(255) not null,
    possible_statuses json         not null,
    current_status    int          not null,
    checked_at        datetime     null,
    created_at        datetime     not null,
    updated_at        datetime     not null,
    last_ping_date    date         null,
    constraint FK_EBF3358C602AD315
        foreign key (responsible_id) references employee (id)
            on delete set null,
    constraint FK_EBF3358CB16D08A7
        foreign key (checklist_id) references checklist (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_EBF3358C602AD315
    on checklist_question (responsible_id);

create index IDX_EBF3358CB16D08A7
    on checklist_question (checklist_id);

create table if not exists checklist_template_question
(
    id                int auto_increment
        primary key,
    responsible_id    int          null,
    checklist_id      int          not null,
    name_pl           varchar(255) not null,
    name_en           varchar(255) not null,
    description_pl    varchar(255) not null,
    description_en    varchar(255) not null,
    possible_statuses json         not null,
    created_at        datetime     not null,
    updated_at        datetime     not null,
    constraint FK_C030E916602AD315
        foreign key (responsible_id) references employee (id)
            on delete set null,
    constraint FK_C030E916B16D08A7
        foreign key (checklist_id) references checklist_template (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_C030E916602AD315
    on checklist_template_question (responsible_id);

create index IDX_C030E916B16D08A7
    on checklist_template_question (checklist_id);

create table if not exists config_entry
(
    id             int auto_increment
        primary key,
    responsible_id int           null,
    config_key     varchar(256)  not null,
    config_value   varchar(2048) not null,
    created_at     datetime      not null,
    replaced_at    datetime      null,
    config_group   varchar(2048) not null,
    constraint FK_960CCF30602AD315
        foreign key (responsible_id) references employee (id)
            on delete set null
)
    collate = utf8_unicode_ci;

create index IDX_960CCF30602AD315
    on config_entry (responsible_id);

create table if not exists contract
(
    id            int auto_increment
        primary key,
    type_id       int        null,
    employee_id   int        null,
    start_date    datetime   null,
    end_date      datetime   null,
    assign_date   datetime   null,
    notice_period int        null,
    active        tinyint(1) not null,
    created_at    datetime   not null,
    updated_at    datetime   not null,
    constraint FK_E98F28598C03F15C
        foreign key (employee_id) references employee (id),
    constraint FK_E98F2859C54C8C93
        foreign key (type_id) references contract_type (id)
)
    collate = utf8_unicode_ci;

create index IDX_E98F28598C03F15C
    on contract (employee_id);

create index IDX_E98F2859C54C8C93
    on contract (type_id);

create index IDX_5D9F75A15FB14BA7
    on employee (level_id);

create index IDX_5D9F75A16F3EE0AD
    on employee (tribe_id);

create index IDX_5D9F75A1DD842E46
    on employee (position_id);

create table if not exists employee_agreement
(
    id           int auto_increment
        primary key,
    employee_id  int          null,
    agreement_id int          null,
    created_at   datetime     not null,
    updated_at   datetime     not null,
    email        varchar(254) null,
    name         varchar(150) null,
    lastname     varchar(150) null,
    constraint FK_2BF5326624890B2B
        foreign key (agreement_id) references agreement (id)
            on delete cascade,
    constraint FK_2BF532668C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    charset = utf8;

create index FK_employee_agreement_agreement
    on employee_agreement (agreement_id);

create table if not exists employee_end_cooperation
(
    id                    int auto_increment
        primary key,
    employee_id           int          null,
    next_company          varchar(254) null,
    who_ended_cooperation varchar(150) null,
    exit_interview        tinyint(1)   null,
    checklist             tinyint(1)   null,
    comment               varchar(500) null,
    created_at            datetime     not null,
    updated_at            datetime     not null,
    name                  varchar(254) null,
    lastname              varchar(254) null,
    position              varchar(254) null,
    dismiss_date          date         null,
    constraint UNIQ_2782FE308C03F15C
        unique (employee_id),
    constraint FK_2782FE308C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create table if not exists evidence
(
    id                   int auto_increment
        primary key,
    employee_id          int           null,
    manager_id           int           null,
    month                smallint      not null,
    year                 smallint      not null,
    working_hours        decimal(5, 2) not null,
    payed_free_hours     decimal(5, 2) not null,
    unpayed_free_hours   decimal(5, 2) not null,
    sick_leave_hours     decimal(5, 2) not null,
    evidence_status      smallint      not null,
    overtime_status      smallint      not null,
    created_at           datetime      null,
    updated_at           datetime      null,
    unavailability_hours decimal(5, 2) not null,
    approver_id          int           null,
    approved             tinyint(1)    null,
    constraint FK_C615710783E3463
        foreign key (manager_id) references employee (id)
            on delete set null,
    constraint FK_C6157108C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade,
    constraint FK_C615710BB23766C
        foreign key (approver_id) references employee (id)
)
    collate = utf8_unicode_ci;

create index IDX_C615710783E3463
    on evidence (manager_id);

create index IDX_C6157108C03F15C
    on evidence (employee_id);

create table if not exists evidence_invoice_attachment
(
    id                int auto_increment
        primary key,
    evidence_id       int          null,
    owner_employee_id int          null,
    path              varchar(100) not null,
    name              varchar(100) not null,
    created_at        datetime     not null,
    updated_at        datetime     not null,
    constraint path
        unique (path),
    constraint FK_1566BC788AE72EDA
        foreign key (owner_employee_id) references employee (id)
            on delete cascade,
    constraint FK_1566BC78B528FC11
        foreign key (evidence_id) references evidence (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_1566BC788AE72EDA
    on evidence_invoice_attachment (owner_employee_id);

create index IDX_1566BC78B528FC11
    on evidence_invoice_attachment (evidence_id);

create table if not exists evidence_overtime
(
    id           int auto_increment
        primary key,
    evidence_id  int           not null,
    project_name varchar(255)  not null,
    project_code varchar(255)  not null,
    hours        decimal(5, 2) not null,
    percentage   smallint      not null,
    time_info    varchar(255)  not null,
    constraint FK_F14A9894B528FC11
        foreign key (evidence_id) references evidence (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_F14A9894B528FC11
    on evidence_overtime (evidence_id);

create table if not exists faq_asked_question
(
    id              int auto_increment
        primary key,
    questioner_id   int        not null,
    faq_category_id int        not null,
    question        mediumblob not null,
    created_at      datetime   not null,
    updated_at      datetime   not null,
    language        tinytext   null,
    constraint FK_196D1211CC0DE6E1
        foreign key (questioner_id) references employee (id)
            on delete cascade,
    constraint FK_196D1211F689B0DB
        foreign key (faq_category_id) references faq_category (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_196D1211CC0DE6E1
    on faq_asked_question (questioner_id);

create index IDX_196D1211F689B0DB
    on faq_asked_question (faq_category_id);

create table if not exists faq_category_responsible
(
    faqcategory_id int not null,
    employee_id    int not null,
    primary key (faqcategory_id, employee_id),
    constraint FK_E8F535158C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade,
    constraint FK_E8F53515B0EF5F16
        foreign key (faqcategory_id) references faq_category (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_771CD07B8C03F15C
    on faq_category_responsible (employee_id);

create index IDX_771CD07BB0EF5F16
    on faq_category_responsible (faqcategory_id);

create table if not exists faq_question
(
    id              int auto_increment
        primary key,
    employee_id     int        not null,
    faq_category_id int        not null,
    question_pl     mediumblob not null,
    question_en     mediumblob not null,
    answer_pl       mediumblob not null,
    answer_en       mediumblob not null,
    created_at      datetime   not null,
    updated_at      datetime   not null,
    constraint FK_4A55B0598C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade,
    constraint FK_4A55B059F689B0DB
        foreign key (faq_category_id) references faq_category (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_4A55B0598C03F15C
    on faq_question (employee_id);

create index IDX_4A55B059F689B0DB
    on faq_question (faq_category_id);

create table if not exists feedback
(
    id                 int auto_increment
        primary key,
    employee_id        int        not null,
    leader_id          int        not null,
    feedback           mediumblob null,
    type               int        not null,
    created_at         datetime   not null,
    updated_at         datetime   not null,
    progress_feedback  mediumblob null,
    technical_feedback mediumblob null,
    date_created       datetime   null,
    constraint FK_D229445873154ED4
        foreign key (leader_id) references employee (id),
    constraint FK_D22944588C03F15C
        foreign key (employee_id) references employee (id)
)
    collate = utf8_unicode_ci;

create index IDX_D229445873154ED4
    on feedback (leader_id);

create index IDX_D22944588C03F15C
    on feedback (employee_id);

create table if not exists fos_user
(
    id                    int auto_increment
        primary key,
    employee_id           int          null,
    username              varchar(180) not null,
    username_canonical    varchar(180) not null,
    email                 varchar(180) not null,
    email_canonical       varchar(180) not null,
    enabled               tinyint(1)   not null,
    salt                  varchar(255) null,
    password              varchar(255) not null,
    last_login            datetime     null,
    confirmation_token    varchar(180) null,
    password_requested_at datetime     null,
    roles                 longtext     not null comment '(DC2Type:array)',
    google_id             varchar(255) null,
    login_expiration      datetime     null,
    login_errors          smallint     not null,
    locked                varchar(255) null,
    constraint UNIQ_957A64798C03F15C
        unique (employee_id),
    constraint UNIQ_957A647992FC23A8
        unique (username_canonical),
    constraint UNIQ_957A6479A0D96FBF
        unique (email_canonical),
    constraint UNIQ_957A6479C05FB297
        unique (confirmation_token),
    constraint FK_957A64798C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create table if not exists leaders
(
    employee_id int not null,
    leader_id   int not null,
    primary key (employee_id, leader_id),
    constraint FK_34C6B9673154ED4
        foreign key (leader_id) references employee (id)
            on delete cascade,
    constraint FK_34C6B968C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_34C6B9673154ED4
    on leaders (leader_id);

create index IDX_34C6B968C03F15C
    on leaders (employee_id);

create table if not exists leave_period
(
    id              int auto_increment
        primary key,
    employee_id     int      not null,
    date_from       date     not null,
    date_to         date     not null,
    freedays        int      not null,
    sick_leave_days int      not null,
    comment_system  text     null,
    created_at      datetime not null,
    updated_at      datetime not null,
    constraint FK_F37E80618C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    charset = utf8;

create index IDX_F37E80618C03F15C
    on leave_period (employee_id);

create table if not exists leave_request
(
    id          int auto_increment
        primary key,
    period_id   int                  null,
    manager_id  int                  null,
    comment     text                 null,
    accepted_at datetime             null,
    status      int                  not null,
    created_at  datetime             not null,
    updated_at  datetime             not null,
    resignation int                  not null,
    hidden      tinyint(1) default 0 not null,
    constraint FK_7DC8F778783E3463
        foreign key (manager_id) references employee (id)
            on delete set null,
    constraint FK_employee_leave_request_period_1
        foreign key (period_id) references leave_period (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index FK_leave_request_employee_2
    on leave_request (manager_id);

create index FK_leave_request_employee_leave_period
    on leave_request (period_id);

create table if not exists leave_request_day
(
    id                int auto_increment
        primary key,
    request_id        int           null,
    type              int           not null,
    status            int           not null,
    period_id         int           null,
    employee_id       int           null,
    hours             int           null,
    date              date          not null,
    avaza_sync_status int default 2 not null,
    avaza_id          varchar(16)   null,
    constraint FK_9B5C8D8B8C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade,
    constraint FK_9B5C8D8BEC8B7ADE
        foreign key (period_id) references leave_period (id)
            on delete cascade,
    constraint FK_leave_request_day_leave_request
        foreign key (request_id) references leave_request (id)
            on delete cascade
)
    collate = utf8_polish_ci;

create index IDX_9B5C8D8B8C03F15C
    on leave_request_day (employee_id);

create index IDX_9B5C8D8BEC8B7ADE
    on leave_request_day (period_id);

create table if not exists links
(
    id         int auto_increment
        primary key,
    author_id  int          not null,
    title      varchar(255) not null,
    url        text         not null,
    created_at datetime     not null,
    updated_at datetime     not null,
    constraint FK_D182A118F675F31B
        foreign key (author_id) references employee (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_D182A118F675F31B
    on links (author_id);

create table if not exists news
(
    id          int auto_increment
        primary key,
    author_id   int                                not null,
    title       varchar(255)                       null,
    description mediumblob                         not null,
    created_at  datetime default CURRENT_TIMESTAMP not null,
    updated_at  datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    type        int      default 0                 not null,
    banner      varchar(255)                       null,
    constraint FK_1DD39950F675F31B
        foreign key (author_id) references employee (id)
            on delete cascade
)
    charset = utf8;

create index IDX_1DD39950F675F31B
    on news (author_id);

create table if not exists planned_feedback
(
    id          int auto_increment
        primary key,
    employee_id int      not null,
    leader_id   int      not null,
    date        date     not null,
    created_at  datetime not null,
    updated_at  datetime not null,
    constraint FK_F3DD980A73154ED4
        foreign key (leader_id) references employee (id)
            on delete cascade,
    constraint FK_F3DD980A8C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_F3DD980A73154ED4
    on planned_feedback (leader_id);

create index IDX_F3DD980A8C03F15C
    on planned_feedback (employee_id);

create index IDX_462CE4F5D5CAD932
    on position (strategy_id);

create table if not exists project
(
    id                      int auto_increment
        primary key,
    name                    varchar(50)                          not null,
    description             text                                 null,
    photo                   varchar(255)                         null,
    url                     varchar(255)                         null,
    started_at              date                                 null,
    ended_at                date                                 null,
    created_at              datetime   default CURRENT_TIMESTAMP not null,
    updated_at              datetime   default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    project_type            int        default 0                 not null,
    sum_type                int        default 0                 not null,
    planned_budget          int                                  null,
    archived                tinyint(1) default 0                 not null,
    billable                tinyint(1)                           not null,
    code                    varchar(255)                         null,
    slack_id                longtext                             null,
    slack_status            smallint                             not null,
    slack_access_token      longtext                             null,
    last_slack_message_sent datetime                             null
)
    charset = utf8;

create table if not exists data_processing_history
(
    id         int auto_increment
        primary key,
    project_id int          not null,
    first_name varchar(255) not null,
    last_name  varchar(255) not null,
    started_at datetime     not null,
    ended_at   datetime     null,
    constraint FK_665122EF166D1F9C
        foreign key (project_id) references project (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_665122EF166D1F9C
    on data_processing_history (project_id);

create table if not exists employee_occupancy
(
    id          int auto_increment
        primary key,
    employee_id int null,
    project_id  int null,
    date        int not null,
    occupancy   int not null,
    constraint unique_entries
        unique (employee_id, project_id, date),
    constraint FK_809E38B4166D1F9C
        foreign key (project_id) references project (id)
            on delete cascade,
    constraint FK_809E38B48C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_809E38B4166D1F9C
    on employee_occupancy (project_id);

create index IDX_809E38B48C03F15C
    on employee_occupancy (employee_id);

create table if not exists employee_project
(
    id          int auto_increment
        primary key,
    employee_id int                  null,
    project_id  int                  null,
    created_at  datetime             not null,
    updated_at  datetime             not null,
    overtime    tinyint(1) default 0 not null,
    date_from   longtext             not null comment '(DC2Type:array)',
    date_to     longtext             not null comment '(DC2Type:array)',
    constraint employee_id_project_id
        unique (employee_id, project_id),
    constraint FK_AFFF86E1166D1F9C
        foreign key (project_id) references project (id)
            on delete cascade,
    constraint FK_AFFF86E18C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    charset = utf8;

create index FK_employee_project_project
    on employee_project (project_id);

create definer = root@localhost trigger employee_project_after_delete
    after delete
    on employee_project
    for each row
BEGIN
UPDATE project_skill_area as psa SET
	psa.value_averaged = (
		SELECT AVG(esa.value_averaged) FROM employee_skill_area as esa
		LEFT JOIN employee_project as ep ON ep.employee_id = esa.employee_id
		WHERE psa.project_id = ep.project_id AND psa.skill_area_id = esa.skill_area_id and esa.value_averaged IS NOT NULL
	);
END;

create definer = root@localhost trigger employee_project_after_insert
    after insert
    on employee_project
    for each row
BEGIN
	UPDATE project_skill_area as psa SET
	psa.value_averaged = (
		SELECT AVG(esa.value_averaged) FROM employee_skill_area as esa
		LEFT JOIN employee_project as ep ON ep.employee_id = esa.employee_id
		WHERE psa.project_id = ep.project_id AND psa.skill_area_id = esa.skill_area_id and esa.value_averaged IS NOT NULL
	);
END;

create definer = root@localhost trigger employee_project_after_update
    after update
    on employee_project
    for each row
BEGIN
	UPDATE project_skill_area as psa SET
	psa.value_averaged = (
		SELECT AVG(esa.value_averaged) FROM employee_skill_area as esa
		LEFT JOIN employee_project as ep ON ep.employee_id = esa.employee_id
		WHERE psa.project_id = ep.project_id AND psa.skill_area_id = esa.skill_area_id and esa.value_averaged IS NOT NULL
	);
END;

create table if not exists gitlab_project_mapping
(
    gitlab_project_id int not null,
    project_id        int not null,
    primary key (gitlab_project_id, project_id),
    constraint FK_BD92D371166D1F9C
        foreign key (project_id) references project (id)
            on delete cascade,
    constraint FK_BD92D3715D07572E
        foreign key (gitlab_project_id) references gitlab_project (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_BD92D371166D1F9C
    on gitlab_project_mapping (project_id);

create index IDX_BD92D3715D07572E
    on gitlab_project_mapping (gitlab_project_id);

create table if not exists integration_queue
(
    id           int auto_increment
        primary key,
    employee_id  int          not null,
    project_id   int          not null,
    type         varchar(255) not null,
    status       int          not null,
    created_at   datetime     not null,
    updated_at   datetime     not null,
    request_data longtext     not null comment '(DC2Type:json)',
    constraint FK_F5AD28B2166D1F9C
        foreign key (project_id) references project (id)
            on delete cascade,
    constraint FK_F5AD28B28C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_F5AD28B2166D1F9C
    on integration_queue (project_id);

create index IDX_F5AD28B28C03F15C
    on integration_queue (employee_id);

create table if not exists project_data_processing_criterium
(
    project_id                   int not null,
    data_processing_criterium_id int not null,
    primary key (project_id, data_processing_criterium_id),
    constraint FK_623B331B166D1F9C
        foreign key (project_id) references project (id)
            on delete cascade,
    constraint FK_623B331B7E5FCB0
        foreign key (data_processing_criterium_id) references data_processing_criterium (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_623B331B166D1F9C
    on project_data_processing_criterium (project_id);

create index IDX_623B331B7E5FCB0
    on project_data_processing_criterium (data_processing_criterium_id);

create table if not exists public_holiday
(
    id               int auto_increment
        primary key,
    date             date         null,
    calculation_type int          null,
    name             varchar(255) not null,
    repeating        tinyint(1)   not null,
    enabled          tinyint(1)   not null,
    created_at       datetime     not null,
    updated_at       datetime     not null
)
    collate = utf8_unicode_ci;

create table if not exists refresh_tokens
(
    id            int auto_increment
        primary key,
    refresh_token varchar(128) not null,
    username      varchar(255) not null,
    valid         datetime     not null,
    constraint UNIQ_9BACE7E1C74F2195
        unique (refresh_token)
)
    collate = utf8_unicode_ci;

create table if not exists sick_leave_attachment
(
    id          int auto_increment
        primary key,
    employee_id int          null,
    path        varchar(100) not null,
    name        varchar(100) not null,
    constraint path
        unique (path),
    constraint FK_BE47A3A58C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_BE47A3A58C03F15C
    on sick_leave_attachment (employee_id);

create table if not exists sick_leave_attachment_download_token
(
    id            int auto_increment
        primary key,
    attachment_id int                                null,
    token         varchar(64)                        not null,
    created_at    datetime default CURRENT_TIMESTAMP not null,
    constraint FK_36A6AF4E464E68B
        foreign key (attachment_id) references sick_leave_attachment (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_36A6AF4E464E68B
    on sick_leave_attachment_download_token (attachment_id);

create table if not exists sick_leave_request_attachment
(
    sick_leave_request_id    int not null,
    sick_leave_attachment_id int not null,
    primary key (sick_leave_request_id, sick_leave_attachment_id),
    constraint FK_sick_leave_request_attachment3_attachment
        foreign key (sick_leave_attachment_id) references sick_leave_attachment (id)
            on delete cascade,
    constraint FK_sick_leave_request_attachment3_request
        foreign key (sick_leave_request_id) references leave_request (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_A43F0640D460611C
    on sick_leave_request_attachment (sick_leave_attachment_id);

create table if not exists skill_area
(
    id             int auto_increment
        primary key,
    name           varchar(80)                        not null,
    description    text                               null,
    value_averaged float    default 0                 null,
    created_at     datetime default CURRENT_TIMESTAMP not null,
    updated_at     datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    charset = utf8;

create table if not exists employee_skill_area
(
    id             int auto_increment
        primary key,
    employee_id    int                                not null,
    skill_area_id  int                                not null,
    value_averaged float    default 0                 null,
    created_at     datetime default CURRENT_TIMESTAMP not null,
    updated_at     datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint employee_id_skill_area_id
        unique (employee_id, skill_area_id),
    constraint FK_802110E8C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade,
    constraint FK_802110EA7FADEB5
        foreign key (skill_area_id) references skill_area (id)
            on delete cascade
)
    charset = utf8;

create index FK__skill_area
    on employee_skill_area (skill_area_id);

create table if not exists project_skill_area
(
    id             int auto_increment
        primary key,
    project_id     int                                not null,
    skill_area_id  int                                not null,
    value_required float    default 0                 null,
    value_averaged float    default 0                 null,
    created_at     datetime default CURRENT_TIMESTAMP not null,
    updated_at     datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint project_id_skill_area_id
        unique (project_id, skill_area_id),
    constraint FK_8873DAE8166D1F9C
        foreign key (project_id) references project (id)
            on delete cascade,
    constraint FK_8873DAE8A7FADEB5
        foreign key (skill_area_id) references skill_area (id)
            on delete cascade
)
    charset = utf8;

create index FK_project_skill_area_skill_area
    on project_skill_area (skill_area_id);

create table if not exists skill
(
    id             int auto_increment
        primary key,
    skill_area_id  int                                not null,
    employee_id    int                                null,
    name           varchar(80)                        not null,
    description    text                               null,
    required       tinyint                            null,
    value_averaged float    default 0                 null,
    created_at     datetime default CURRENT_TIMESTAMP not null,
    updated_at     datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint FK_5E3DE4778C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade,
    constraint FK_5E3DE477A7FADEB5
        foreign key (skill_area_id) references skill_area (id)
            on delete cascade
)
    charset = utf8;

create table if not exists employee_skill
(
    id            int auto_increment
        primary key,
    employee_id   int      null,
    skill_id      int      null,
    value         double   null,
    created_at    datetime not null,
    updated_at    datetime not null,
    skill_area_id int      null,
    constraint employee_id_skill_id
        unique (employee_id, skill_id),
    constraint FK_B630E90E5585C142
        foreign key (skill_id) references skill (id)
            on delete cascade,
    constraint FK_B630E90E8C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade,
    constraint FK_B630E90EA7FADEB5
        foreign key (skill_area_id) references skill_area (id)
            on delete cascade
)
    charset = utf8;

create index FK__skill
    on employee_skill (skill_id);

create index IDX_B630E90EA7FADEB5
    on employee_skill (skill_area_id);

create table if not exists employee_skill_history
(
    id          int auto_increment
        primary key,
    employee_id int      default 0                 not null,
    skill_id    int      default 0                 not null,
    value_old   float    default 0                 not null,
    value_new   float    default 0                 not null,
    created_at  datetime default CURRENT_TIMESTAMP not null,
    updated_at  datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint FK_4BBD9AD95585C142
        foreign key (skill_id) references skill (id)
            on delete cascade,
    constraint FK_4BBD9AD98C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    charset = utf8;

create index FK_employee_skill_history_employee
    on employee_skill_history (employee_id);

create index FK_employee_skill_history_skill
    on employee_skill_history (skill_id);

create index FK_skill_employee
    on skill (employee_id);

create index FK_skill_skill_area
    on skill (skill_area_id);

create table if not exists tribe
(
    id                      int auto_increment
        primary key,
    name                    varchar(255)                       not null,
    description             text                               null,
    photo                   varchar(255)                       null,
    url                     varchar(255)                       null,
    created_at              datetime default CURRENT_TIMESTAMP not null,
    updated_at              datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    is_virtual              tinyint(1)                         not null,
    visibility              int                                null,
    slack_id                longtext                           null,
    slack_status            smallint                           not null,
    slack_access_token      longtext                           null,
    last_slack_message_sent datetime                           null,
    sick_leave_project_id   varchar(50)                        null,
    sick_leave_category_id  varchar(50)                        null,
    free_day_project_id     varchar(50)                        null,
    free_day_category_id    varchar(50)                        null,
    hr_email                varchar(254)                       not null,
    tech_leader             int                                null,
    constraint FK_2653B558E5EE8BEC
        foreign key (tech_leader) references employee (id)
            on delete set null
)
    charset = utf8;

alter table employee
    add constraint FK_5D9F75A16F3EE0AD
        foreign key (tribe_id) references tribe (id)
            on delete set null;

create table if not exists position_tribe
(
    position_id int not null,
    tribe_id    int not null,
    primary key (position_id, tribe_id),
    constraint FK_AA9351856F3EE0AD
        foreign key (tribe_id) references tribe (id)
            on delete cascade,
    constraint FK_AA935185DD842E46
        foreign key (position_id) references position (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_AA9351856F3EE0AD
    on position_tribe (tribe_id);

create index IDX_AA935185DD842E46
    on position_tribe (position_id);

create table if not exists potential_employee
(
    id                     int auto_increment
        primary key,
    designated_tribe_id    int          null,
    designated_position_id int          null,
    joined_employee_id     int          null,
    name                   varchar(150) not null,
    lastname               varchar(150) not null,
    email                  varchar(50)  not null,
    designated_hire_date   date         null,
    status                 int          not null,
    rejection_cause        varchar(500) null,
    created_at             datetime     not null,
    updated_at             datetime     not null,
    gender                 int          not null,
    date_of_birth          datetime     not null,
    private_phone          varchar(50)  not null,
    remote                 tinyint(1)   not null,
    city                   varchar(50)  null,
    postal_code            varchar(50)  null,
    street                 varchar(100) null,
    recruiter_id           int          null,
    private_email          varchar(50)  not null,
    source                 varchar(50)  null,
    company                varchar(50)  null,
    nip                    varchar(15)  null,
    firm_name              varchar(255) null,
    firm_address           varchar(255) null,
    welcome_day_date       date         null comment '(DC2Type:date_immutable)',
    language               varchar(2)   not null,
    country                varchar(50)  null,
    contract_type          varchar(30)  not null,
    work                   tinyint(1)   not null,
    outsource_sub_type     varchar(255) null,
    constraint UNIQ_26DD286331AACC7
        unique (joined_employee_id),
    constraint FK_26DD2863156BE243
        foreign key (recruiter_id) references employee (id)
            on delete set null,
    constraint FK_26DD286331AACC7
        foreign key (joined_employee_id) references employee (id)
            on delete set null,
    constraint FK_26DD28639C02A8D5
        foreign key (designated_position_id) references position (id)
            on delete set null,
    constraint FK_26DD2863BCE1E7BB
        foreign key (designated_tribe_id) references tribe (id)
            on delete set null
)
    collate = utf8_unicode_ci;

create table if not exists employee_hardware
(
    id                    int auto_increment
        primary key,
    employee_id           int                  null,
    category              varchar(100)         null,
    manufacturer          varchar(100)         null,
    model                 varchar(100)         null,
    serialNumber          varchar(100)         null,
    asset_id              int                  not null,
    send_email            tinyint(1) default 0 not null,
    potential_employee_id int                  null,
    constraint FK_6EA6B82789EFB0AC
        foreign key (potential_employee_id) references potential_employee (id)
            on delete set null,
    constraint FK_6EA6B8278C03F15C
        foreign key (employee_id) references employee (id)
            on delete set null
)
    collate = utf8_unicode_ci;

create index IDX_6EA6B82789EFB0AC
    on employee_hardware (potential_employee_id);

create index IDX_6EA6B8278C03F15C
    on employee_hardware (employee_id);

create table if not exists hardware_agreement
(
    id                 int auto_increment
        primary key,
    signed_by_helpdesk datetime null,
    signed_by_user     datetime null,
    pesel              blob     null,
    nip                blob     null,
    company            blob     null,
    headquarters       blob     null,
    password_hashed    tinytext null,
    assignment_id      int      null,
    helpdesk_signer_id int      null,
    use_languages      tinytext not null comment '(DC2Type:simple_array)',
    date_generated     datetime null,
    employeeName       tinytext null,
    employeeLastName   tinytext null,
    created_at         datetime not null,
    updated_at         datetime not null,
    constraint UNIQ_C6002FA6D19302F8
        unique (assignment_id),
    constraint FK_C6002FA615E7B47B
        foreign key (helpdesk_signer_id) references employee (id)
            on delete cascade,
    constraint FK_C6002FA6D19302F8
        foreign key (assignment_id) references employee_hardware (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_C6002FA615E7B47B
    on hardware_agreement (helpdesk_signer_id);

create index IDX_26DD2863156BE243
    on potential_employee (recruiter_id);

create index IDX_26DD28639C02A8D5
    on potential_employee (designated_position_id);

create index IDX_26DD2863BCE1E7BB
    on potential_employee (designated_tribe_id);

create index IDX_2653B558E5EE8BEC
    on tribe (tech_leader);

create table if not exists tribe_responsible
(
    tribe_id    int not null,
    employee_id int not null,
    primary key (tribe_id, employee_id),
    constraint FK_7E38AF476F3EE0AD
        foreign key (tribe_id) references tribe (id)
            on delete cascade,
    constraint FK_7E38AF478C03F15C
        foreign key (employee_id) references employee (id)
            on delete cascade
)
    collate = utf8_unicode_ci;

create index IDX_7E38AF476F3EE0AD
    on tribe_responsible (tribe_id);

create index IDX_7E38AF478C03F15C
    on tribe_responsible (employee_id);

create table if not exists tribe_rotation_history
(
    id               int auto_increment
        primary key,
    tribe_name       varchar(50)   not null,
    number_of_enter  int default 0 not null,
    number_of_leave  int default 0 not null,
    number_of_work   int default 0 not null,
    number_of_male   int default 0 not null,
    number_of_female int default 0 not null,
    year             smallint      not null,
    month            smallint      not null,
    employees        varchar(255)  not null,
    created_at       datetime      not null,
    updated_at       datetime      not null
)
    collate = utf8_unicode_ci;

