# GET /api/projects/details

Returns detailed list of all projects.

```
curl --request GET \
  --url 'URL/api/projects/details \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_USER` role or above.

## Result
JSON array containing project objects:

```json
[
  {
    "id": 15,
    "name": "Super project",
    "description": "This is a description of super project",
    "photo": "https://super-project.tld/logo.png",
    "url": "https://super-project.tld",
    "started_at": 123456789,
    "ended_at": 123456789,
    "project_type": 1,
    "sum_type": 1,
    "planned_budget": 150,
    "criteria": [
      {
        "id": 5,
        "namePl": "Nazwisko",
        "nameEn": "Last name"
      },
      ...
    ],
    "visibility": 1,
    "billable": true,
    "code": "SUPP",
    "toggl_projects": [
      {
        "id": 1,
        "name": "Project name in Toggl"
      },
      ...
    ],
    "gitlab_projects": [
      {
        "id": 1,
        "type": 1,
        "name": "Repository name in GitLab"
      },
      ...
    ],
    "connectedToSlack": true
  },
  ...
]
```

* `id` - Project's ID
* `name` - Project's name
* `description` - Projects's description
* `photo` - URL to project's logo or photo
* `url` - URL to project's main page
* `started_at` - Timestamp for starting project
* `ended_at` - Timestamp for ending project
* `project_type` - Type of project (0 - undefined, 1 - implementation, 2 - maintenance)
* `sum_type` - Type of summing project hours (0 - monthly, 1 - for whole project)
* `planned_budget` - planned budget in hours
* `criteria` - list of all criteria for given project
    * `id` - Criterion's ID
    * `namePl` - Criterion's name in Polish
    * `nameEn` - Criterion's name in English
* `visibility` - ???
* `billable` - whether project is billable or not
* `code` - code used internally by company
* `toggl_projects` - list of all enabled Toggl integrations 
    * `id` - Toggl integration ID
    * `name` - Toggl project name
* `gitlab_projects` - list of all enabled GitLab integrations
    * `id` - GitLab integration ID
    * `type` - Integration type (0 - repository, 1 - group)
    * `name` - GitLab repository/group name
* `connectedToSlack` - if `true`, integration with Slack is enabled for this project