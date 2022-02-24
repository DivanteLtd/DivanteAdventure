# POST /api/projects

Creates new project.

```
curl --request POST \
  --url 'URL/api/projects\
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
	"name": "Super project"
  }'
```

### Security requirements
`ROLE_MANAGER` role or above.

## Request
JSON object containing following fields:
```json
{
  "name": "Super project",
  "description": "This is a description of super project",
  "photo": "https://super-project.tld/logo.png",
  "url": "https://super-project.tld",
  "started_at": 123456789,
  "ended_at": 123456789,
  "project_type": 1,
  "planned_budget": 150,
  "billable": true,
  "code": "SUPP",
  "toggl_projects": [ 420, 69 ],
  "gitlab_projects": [ 21, 37 ]
}
```

* `name` - Project's name
* `description` - Projects's description
* `photo` - URL to project's logo or photo
* `url` - URL to project's main page
* `started_at` - Timestamp for starting project
* `ended_at` - Timestamp for ending project
* `project_type` - Type of project (0 - undefined, 1 - implementation, 2 - maintenance)
* `planned_budget` - planned budget in hours
* `billable` - whether project is billable or not
* `code` - code used internally by company
* `toggl_projects` - list of IDs of all enabled Toggl integrations 
* `gitlab_projects` - list of IDs of all enabled GitLab integrations

## Result
Empty JSON object.
