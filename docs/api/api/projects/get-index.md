# GET /api/projects

Returns simplified list of all projects.

```
curl --request GET \
  --url 'URL/api/projects \
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
    "startedAt": "123456789",
    "endedAt": "123456789"
  },
  ...
]
```

* `id` - Project's ID
* `name` - Project's name
* `startedAt` - Timestamp for starting project
* `endedAt` - Timestamp for ending project
