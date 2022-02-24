# GET /api/feedback/{id}

Returns array of objects representing all feedbacks information of employee with given id.

```
curl --request GET \
  --url 'URL/api/feedback/id \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
* `ROLE_USER` or above - user can get feedback for himself or for his current padawan
* `ROLE_TRIBE_MASTER` or above - get feedback for employee from his tribe
* `ROLE_SUPER_ADMIN` - get feedback for anyone
## Result
JSON array containing feedback objects:

```json
[
  {
    "id": 150,
    "employee": { ... },
    "leader": { ... },
    "feedback": "John did a great job",
    "type": 1,
    "updatedAt": 1234567890
  },
  ...
]
```
* `id` - Feedback's ID
* `employee` - mapped object representing owner of that feedback
* `leader` - mapped object representing leader who created that feedback
* `feedback` - Feedback text
* `type` - 1 if technical feedback, 2 if feedback from tribe master
