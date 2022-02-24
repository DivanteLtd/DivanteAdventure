# GET /api/employees/{id}/checklists

Returns list of Employee's checklists.

```
curl --request GET \
  --url 'URL/api/employees/200/checklists \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_SUPER_ADMIN` role.

## Result

JSON array containing all checklists, in which Employee is either owner or subject, or both:

```json
[
  {
    "id": 15,
    "type": 1,
    "namePl": "Nazwa listy po polsku",
    "nameEn": "List name in English",
    "subject": {
      "id": 200,
      "name": "John",
      "lastName": "Smith",
      "photo": "http://page.tld/photo.jpg"
    },
    "owner": null,
    "startedAt": 1234567890,
    "finishedAt": null,
    "tasksFinishedCount": 5,
    "tasksAllCount": 7
  },
  ...
]
```

* `id` - checklist ID
* `type` - 1 if united, 2 if distributed
* `namePl` - checklist name in Polish
* `nameEn` - checklist name in English
* `subject` - subject employee data
    * `id` - Employee's ID
    * `name` - Employee's name
    * `lastName` - Employee's last name
    * `photo` - URL to Employee's avatar
* `owner` - owner employee data, in the same structure as `subject`. Can be null.
* `startedAt` - timestamp marking time when checklist was started.
* `finishedAt` - timestamp marking time when checklist was finished, or null if it wasn't finished yet.
* `tasksFinishedCount` - count of tasks in checklist marked as finished.
* `tasksAllCount` - count of all tasks in checklist.