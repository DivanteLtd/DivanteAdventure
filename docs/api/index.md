# Adventure API

## Basic information

### URL and TOKEN variables in documentation

In entrypoints list, every entrypoint is described with sample cURL request. This request will contain "URL" variable,
which corresponds to full URL to Adventure backend. In case of production environment used by Divante, while frontend is
located in `https://adventure.divante.pl`, backend is located in `https://adventure.divante.pl:8181`. The other common
variable is "TOKEN", which contains Bearer token aquired during login.

Sample cURL in documentation:

```
curl --request GET \
  --url URL/api/leaveRequest \
  --header 'authorization: Bearer TOKEN'
```

for production environment and access token equal to "ABC12345" cURL above will correspond to this:
```
curl --request GET \
  --url https://adventure.divante.pl:8181/api/leaveRequest \
  --header 'authorization: Bearer ABC12345'
```

### JWT token

Currently the only way to acquire JWT token is during login. After clicking "Login" on frontend user is redirected to
backend URL `URL/connect/google`. This entrypoint checks parameters for connection with Google and uses it to access
user data from Google. Google redirects back to `URL/login/check-google`, which then redirects back to frontend with
generated JWT token. The user is created now if wasn't existing before.

JWT is generated and encrypted with RS256 algorithm, so header of this JWT is:
```json
{
  "typ": "JWT",
  "alg": "RS256"
}
```

Body is made of following fields:
```json
{
  "iat": 1580709294,
  "exp": 1580795694,
  "roles": {
    "0": "ROLE_USER",
    "2": "ROLE_SUPER_ADMIN",
    "3": "ROLE_HELPDESK",
    "5": "ROLE_TRIBE_MASTER",
    "6": "ROLE_MANAGER",
    "8": "ROLE_HR"
  },
  "username": "jkowalski@example.com",
  "ip": "312.353.123.15",
  "employeeId": 123
}
```

* `iat` - timestamp when token was issued
* `exp` - timestamp when token will expire
* `roles` - contains list user's roles. Can be either array of strings or, as in sample above, object. If `roles` is an
    object, keys don't have any importance and can be safely ignored.
* `username` - e-mail used for logging in to Google
* `ip` - user's IP address
* `employeeId` - user's ID in Adventure

### Roles

Following roles currently exist:
* `ROLE_USER` (default role, which every logged in user has)
* `ROLE_MANAGER` (extends `USER`)
* `ROLE_HR` (extends `USER`)
* `ROLE_HELPDESK` (extends `USER`)
* `ROLE_TRIBE_MASTER` (extends `MANAGER` and `HR`)
* `ROLE_SUPER_ADMIN` (extends `TRIBE_MASTER` and `HELPDESK`)

## List of entrypoints

Entrypoints, sorted alphabetically by URL, then by methods in order: GET, POST, PATCH, PUT, DELETE

| Method            | URL                                     | Documentation                                      |
|:-----------------:|:----------------------------------------|:---------------------------------------------------|
| ![GET][GET]       | `/api/config`                           | [Link](./api/config/get-index.md)                  |
| ![POST][POST]     | `/api/config`                           | [Link](./api/config/post-index.md)                 |
| ![GET][GET]       | `/api/config/{entry}`                   | [Link](./api/config/get-history.md)                |
| ![GET][GET]       | `/api/employees`                        | [Link](./api/employees/get-index.md)               |
| ![PATCH][PATCH]   | `/api/employees/{id}`                   | [Link](./api/employees/patch-id.md)                |
| ![DELETE][DELETE] | `/api/employees/{id}`                   | [Link](./api/employees/delete-id.md)               |
| ![GET][GET]       | `/api/employees/{id}/checklists`        | [Link](./api/employees/get-id-checklists.md)       |
| ![POST][POST]     | `/api/employees/assign/tribe/{id}`      | [Link](./api/employees/post-assign-tribe-id.md)    |
| ![GET][GET]       | `/api/employees/details`                | [Link](./api/employees/get-details.md)             |
| ![GET][GET]       | `/api/employees/endedWork`              | [Link](./api/employees/get-ended-work.md)          |
| ![POST][POST]     | `/api/employees/endedWork`              | [Link](./api/employees/post-ended-work.md)         |
| ![PATCH][PATCH]   | `/api/employees/endedWork/{id}`         | [Link](./api/employees/patch-ended-work-id.md)     |
| ![DELETE][DELETE] | `/api/employees/endedWork/{id}`         | [Link](./api/employees/delete-ended-work-id.md)    |
| ![GET][GET]       | `/api/employees/firstHiredDate`         | [Link](./api/employees/get-first-hired-date.md)    |
| ![GET][GET]       | `/api/employees/hardware/{id}`          | [Link](./api/employees/get-hardware-id.md)         |
| ![POST][POST]     | `/api/employees/hideSlack`              | [Link](./api/employees/post-hide-slack.md)         |
| ![GET][GET]       | `/api/employees/id/{id}`                | [Link](./api/employees/get-id-id.md)               |
| ![GET][GET]       | `/api/employees/isPinSet`               | [Link](./api/employees/get-is-pin-set.md)          |
| ![POST][POST]     | `/api/employees/unassign/tribe/{id}`    | [Link](./api/employees/post-unassign-tribe-id.md)  |
| ![POST][POST]     | `/api/employees/unlock/{id}`            | [Link](./api/employees/post-unlock-id.md)          |
| ![POST][POST]     | `/api/employees/verifyPin`              | [Link](./api/employees/post-verify-pin.md)         |
| ![GET][GET]       | `/api/employees/workLocation`           | [Link](./api/employees/get-work-location.md)       |
| ![POST][POST]     | `/api/employees/workLocation`           | [Link](./api/employees/post-work-location.md)      |
| ![DELETE][DELETE] | `/api/employees/workLocation/{id}`      | [Link](./api/employees/delete-work-location-id.md) |
| ![GET][GET]       | `/api/employees/workLocation/all`       | [Link](./api/employees/get-work-location-all.md)   |
| ![GET][GET]       | `/api/news`                             | [Link](./api/news/get-index.md)                    |
| ![POST][POST]     | `/api/news`                             | [Link](./api/news/post-index.md)                   |
| ![PATCH][PATCH]   | `/api/news/{id}`                        | [Link](./api/news/patch-id.md)                     |
| ![DELETE][DELETE] | `/api/news/{id}`                        | [Link](./api/news/delete-id.md)                    |
| ![GET][GET]       | `/api/period`                           | [Link](./api/period/get-index.md)                  |
| ![POST][POST]     | `/api/period`                           | [Link](./api/period/post-index.md)                 |
| ![GET][GET]       | `/api/period/{id}`                      | [Link](./api/period/get-id.md)                     |
| ![PATCH][PATCH]   | `/api/period/{id}`                      | [Link](./api/period/patch-id.md)                   |
| ![DELETE][DELETE] | `/api/period/{id}`                      | [Link](./api/period/delete-id.md)                  |
| ![GET][GET]       | `/api/period/report`                    | [Link](./api/period/get-report.md)                 |
| ![GET][GET]       | `/api/potential_employee`               | [Link](./api/potential_employee/get-index.md)      |
| ![POST][POST]     | `/api/potential_employee`               | [Link](./api/potential_employee/post-index.md)     |
| ![PATCH][PATCH]   | `/api/potential_employee/{id}`          | [Link](./api/potential_employee/patch-id.md)       |
| ![DELETE][DELETE] | `/api/potential_employee/{id}`          | [Link](./api/potential_employee/delete-id.md)      |
| ![GET][GET]       | `/api/projects`                         | [Link](./api/projects/get-index.md)                |
| ![POST][POST]     | `/api/projects`                         | [Link](./api/projects/post-index.md)               |
| ![PATCH][PATCH]   | `/api/projects/{id}`                    | [Link](./api/projects/patch-id.md)                 |
| ![DELETE][DELETE] | `/api/projects/{id}`                    | [Link](./api/projects/delete-id.md)                |
| ![POST][POST]     | `/api/projects/{id}/criterium`          | [Link](./api/projects/post-id-criterium.md)        |
| ![DELETE][DELETE] | `/api/projects/{id}/criterium/{critId}` | [Link](./api/projects/delete-id-criterium-id.md)   |
| ![POST][POST]     | `/api/projects/{id}/disconnectSlack`    | [Link](./api/projects/post-id-disconnect-slack.md) |
| ![GET][GET]       | `/api/projects/details`                 | [Link](./api/projects/get-details.md)              |
| ![PATCH][PATCH]   | `/api/projects/hide/{id}`               | [Link](./api/projects/patch-hide-id.md)            |
| ![GET][GET]       | `/api/projects/sendEmail/{id}`          | [Link](./api/projects/get-send-email-id.md)        |
| ![GET][GET]       | `/api/tribe`                            | [Link](./api/tribe/get-index.md)                   |
| ![POST][POST]     | `/api/tribe`                            | [Link](./api/tribe/post-index.md)                  |
| ![PATCH][PATCH]   | `/api/tribe/{id}`                       | [Link](./api/tribe/patch-id.md)                    |
| ![DELETE][DELETE] | `/api/tribe/{id}`                       | [Link](./api/tribe/delete-id.md)                   |
| ![POST][POST]     | `/api/tribe/{id}/disconnectSlack`       | [Link](./api/tribe/post-id-disconnect-slack.md)    |
| ![POST][POST]     | `/api/tribe/{id}/position/{posId}`      | [Link](./api/tribe/post-id-position-id.md)         |
| ![DELETE][DELETE] | `/api/tribe/{id}/position/{posId}`      | [Link](./api/tribe/delete-id-position-id.md)       |

[GET]: https://img.shields.io/static/v1?label=&message=GET&color=green
[POST]: https://img.shields.io/static/v1?label=&message=POST&color=blue
[PATCH]: https://img.shields.io/static/v1?label=&message=PATCH&color=blueviolet
[PUT]: https://img.shields.io/static/v1?label=&message=PUT&color=yellow
[DELETE]: https://img.shields.io/static/v1?label=&message=DELETE&color=red