# GET /api/tribe

Returns list of all tribes.

```
curl --request GET \
  --url 'URL/api/tribe \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_USER` role or above.

## Result

JSON array containing Tribe objects.

Sample result:

```json
[
  {
    "id": 1,
    "name": "Super tribe",
    "photoUrl": "https://sample.tld/photo.png",
    "url": "https://super-tribe-page.sample.tld",
    "isVirtual": false,
    "visibility": 1,
    "description": "Super tribe description",
    "employeesCount": 15,
    "positions": [
      {
        "id": 1,
        "name": "Backend developer",
        "createdAt": 1234567890,
        "updatedAt": 1234567890,
        "employeeCount": 15,
        "strategy": { ... }
      },
      ...
    ],
    "connectedToSlack": true,
    "responsible": [
       {
         "id": 200,
         "name": "John",
         "lastName": "Smith",
         "photo": "https://sample.tld/photo.png"
        },
        { ... }
    ],
  },
  ...
]
```

* `id` - Tribe's ID
* `name` - Tribe's name
* `photoUrl` - URL to tribe's photo
* `url` - URL address to show as tribe page
* `isVirtual` - if `true`, tribe cannot have any projects
* `visibility` - ???
* `description` - Tribe's description
* `emplyoeesCount` - how many employees does tribe have
* `positions` - array containing Position objects representing positions attached to that tribe
* `connectedToSlack` - if `true`, tribe has enabled Slack integration. 
* `responsible` - mapped array of object representing responsible employees of that tribe