# Adventure - Open HRM

**Adventure is a kind of ERP (Enterprise Resource Planning) for service-oriented and/or software companies.** 
It's internally used at Divante eCommerce Company. It's a central communication and planning system where our HR,
Administration, and Finance departments process the necessary data about employees, projects, skills, and assignments.
It's neither the HRM neither the CRM. It's both with enterprise intranet features for managing the internal relationship
with all the necessary information at the management team's disposal, including information on signed consents, planned
holidays, and many others.

We feel that many other service-oriented companies might have pretty much the same needs therefore we decided to Open
Source our project. Please find the full features list in the index below.

## See it in action

<table>
    <tbody>
        <tr>
            <td align="center" valign="middle">
                <a href="https://adventure-demo.divante.com">
                    <img src="https://gitlab.divante.pl/DivanteAdventure/adventure/raw/docs-updates/docs/screenshots/dashboard.png"
                         alt="Adventure demo"/>
                </a>
            </td>
            <td align="left" valign="top">
                Try out <a href="https://adventure-demo.divante.com/">our demo</a> and if you like it <strong>consider 
                giving us some stars on GitHub ★</strong>. After login you should have full admin access. We're clearing
                demo data automatically every 6 hours. Integrations (with Slack, Avaza, etc.) and e-mail messages do not
                work in demo environment.
            </td>
        </tr>
    </tbody>
</table>

## Features

*Keep yourself updated*: See company news on your dashboard or write a news for all of your coworkers

*Apply for a free day*:  You can ask your manager for a free day or notify about sick leave. Your administration
department will be notified about that, too, reducing bureaucracy.

*Handle internal documents*: Documents like GDPR agreements can be handled via Adventure. Users are able to accept
such documents, while administration can check acceptance list.

*Divide work into checklists*: Describe your company's processess (i.e. what to do when new employee joins
your firm) as list of tasks, set responsibilities and monitor process status.

## Getting started

Fork repository, clone it into your computer 
Create/Configure files properly:
* .env based on .env.example
* backend/app/config/parameters.yml based on parameters.yml.dist
* backend/app/config/parameters-test.yml based on parameters.yml.dist
* frontend/adventure_config.js based on adventure_config.js.example
and run `make dev`. 
That's it! You'll find more detailed
information on [development setup](./docs/project-setup.md) and [development tools](./docs/dev-tools.md) docs
pages. Remember to read our [contributing guide](./CONTRIBUTING.md).

[API documentation (not complete)](./docs/api/index.md)
