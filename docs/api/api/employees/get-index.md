# GET /api/employees

Returns list of employees to use for planner.

```
curl --request GET \
  --url 'URL/api/employees?query=QUERY' \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_MANAGER` role or above.

## Parameters

* `query` (not required) - query string put by user to filter field on planner.

## Result

JSON array containing Employee objects, filtered by `query` parameter if supplied.

Sample result:

```json
[
  {
    "id": 200,
    "name": "John",
    "lastName": "Smith",
    "worktime": 21600,
    "tribeName": "Tribe name",
    "positionName": "Backend developer",
    "levelName": "Senior",
    "projectName": "SomeProject",
    "filters": [ "skill/1", "skill/2", "position/4" ]
  },
  ...
]
```

Fields:
* `id` - Employee's ID
* `name` - Employee's name
* `lastName` - Employee's last name
* `worktime` - Worktime in seconds per day
* `tribeName` - Employee's tribe name
* `positionName` - Employee's position name
* `levelName` - Employee's level name
* `projectName` - ???
* `filters` - string array containing filter tags for additional frontend filtering, i.e. if user wants to see every
    employee with skill with ID 6, user can filter resulting array by looking for all employees that contain `skill/6`
    inside `filters` array.