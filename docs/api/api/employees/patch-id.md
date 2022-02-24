# PATCH /api/employees/{id}

Updates data of employee with given ID.

```
curl --request PATCH \
  --url 'URL/api/employees/200' \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
	"lastName": "Smith"
  }'
```

### Security requirements
`ROLE_USER` role for editing self, `ROLE_SUPER_ADMIN` role for editing other accounts.

## Result

JSON object representing edited employee. Content of JSON object depends on users role.

Sample result for highest role:

```json
{
  "id": 200,
  "name": "John",
  "lastName": "Smith",
  "photo": "https://sample.tld/photo.png",
  "phone": "123456789",
  "position": {
    "id": 1,
    "name": "Backend developer",
    "createdAt": 1234567890,
    "updatedAt": 1234567890,
    "employeeCount": 15,
    "strategy": { ... }
  },
  "level": {
    "id": 1,
    "name": "Senior",
    "createdAt": 1234567890,
    "updatedAt": 1234567890,
    "employeeCount": 15,
    "priority": 5,
    "strategy": { ... }
  },
  "tribe": {
    "id": 1,
    "name": "Tribe name",
    "description": "Tribe description"
  },
  "remote": true,
  "city": "Wrocław",
  "email": "jsmith@example.com",
  "manager": false,
  "licencePlate": "ABCD123",
  "gender": 0,
  "freeToday": false,
  "roles": [ "ROLE_USER" ],
  
  "worktime": 28800,
  "jobTimeValue": 28800,
  "childCare": 0,
  
  "hiredAt": "2019-01-01",
  "hiredTo": null,
  "dateOfBirth": "1997-10-21",
  "contract": {
    "id": 1,
    "name": "CoE"
  },
  "privatePhone": "1234567890",
  
  "emergencyFirstName": "John",
  "emergencyLastName": "Doe",
  "emergencyAddress": "",
  "emergencyPhone": "1234567890",

  "togglApiKey": "ABCDEFGHIJKLM",
  "locked": false,
  "slackStatus": 1,
  "agreementsRequired": false,
  "hasSetPin": true,
  "language": "pl"
}
```

Fields for `ROLE_USER` and above:

* `id` - Employee's ID
* `name` - Employee's name
* `lastName` - Employee's last name
* `photo` - URL address to photo
* `phone` - public (company) phone number
* `position` - object describing Employee's position
    * `id` - Position ID
    * `name` - Position name
    * `createdAt` - creation timestamp
    * `updatedAt` - last update timestamp
    * `employeeCount` - count of employees using this position
    * `strategy` - object describing leveling strategy of this position
* `level` - object describing Employee's level
    * `id` - Level ID
    * `name` - Level name
    * `createdAt` - creation timestamp
    * `updatedAt` - last update timestamp
    * `employeeCount` - count of employees using this level
    * `priority` - order of displaying levels in one strategy
    * `strategy` - object describing leveling strategy of this level
* `tribe` - object describing Employee's tribe
    * `id` - Tribe ID
    * `name` - Tribe name
    * `description` - Tribe description
* `remote` - if `true`, Employee is working remotely
* `city` - city where Employee is working
* `email` - Employee's e-mail address
* `manager` - `true` if Employee has role `ROLE_MANAGER` or above
* `licencePlate` - licence plate of Employee's car
* `gender` - 0 if male, 1 if female
* `freeToday` - `true` if Employee has a free day today
* `roles` - array containing all of Employee's roles

Fields for `ROLE_MANAGER` and above:
* `worktime` - Worktime in seconds per day
* `jobTimeValue` - Worktime in seconds per day
* `childCare` - if `1`, user can use child care hours.

Fields for `ROLE_HR` and above:
* `hiredAt` - `YYYY-MM-DD`-formatted date when Employee started work
* `hiredTo` - `YYYY-MM-DD`-formatted date when Employee ends work in company
* `dateOfBirth` - `YYYY-MM-DD`-formatted Employee's birthday
* `contract` - object describing Employee's contract
    * `id` - Contract ID
    * `name` - Contract name ("CoE", "CLC LUMP SUM", "CLC HOURLY", "B2B LUMP SUM" or "B2B HOURLY")
* `privatePhone` - Employee's private phone number

Fields for `ROLE_TRIBE_MASTER` and above:
* `emergencyFirstName` - Name of contact person in the event of an accident
* `emergencyLastName` - Last name of contact person in the event of an accident
* `emergencyAddress` - (deprecated; empty string, will be removed)
* `emergencyPhone` - Phone number of contact person in the event of an accident

Fields for `ROLE_SUPER_ADMIN`:
* `togglApiKey` - API token for communications with Toggl
* `locked` - if `true`, user has failed PIN authorization three times
* `slackStatus` - current status of Slack integration (0 - user has not been asked for integration yet;
    1 - user was asked and decided not to enable integration or disabled it after integration; 2 - user decided to
    integrate and Adventure is waiting for token from Toggl; 3 - integration is fully enabled)
* `agreementsRequired` - if `true`, there are some agreements that user must accept
* `hasSetPin` - if `true`, user has set PIN
* `language` - language used by user; either "pl" or "en".