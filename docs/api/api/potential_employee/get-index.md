# GET /api/potential_employee

Returns list of potential employees.

```
curl --request GET \
  --url "URL/api/potential_employee \
  --header "authorization: Bearer TOKEN"
```

### Security requirements
`ROLE_HR` role or above.

## Result
JSON array containing potential employee objects:

```json
[
  {
    "id": 15,
    "createdAt": 123456789,
    "updatedAt": 123456789,
    "name": "John",
    "lastName": "Smith",
    "email": "jsmith@example.com",
    "status": 1,
    "rejectionCause": null,
    "hireDate": "2010-12-31",
    "position": { ... },
    "tribe": { ... },
    "joinedEmployee": { ... },
    "gender": 1,
    "dateOfBirth": "2010-12-31",
    "privatePhone": 123456789,
    "remote": true,
    "city": "Wroclaw",
    "postalCode": "50-231",
    "street": "Dmowskiego 7A",
    "privateEmail": "jsmith@example.com",
    "source": "example.com",
    "trip": true,
    "company": "SoftwareHouse 123"
  },
  ...
]
```

* `id` - Potential employee"s ID
* `createdAt` - timestamp when potential employee was created
* `updatedAt` - timestamp when potential employee was updated last time
* `name` - employee"s name
* `lastName` - employee"s last name
* `email` - suggested email for a potential employee
* `status` - "0" if potential employee, "1" if employee was hired, "2" if employee was rejected
* `rejectionCause` - cause of rejection
* `hireDate` - date in YYYY-MM-DD format when employee was or will be hired
* `position` - object describing employee"s potential position
* `tribe` - object describing employee"s potential tribe
* `joinedEmployee` - if potential employee was hired, object describing Employee entity of this potential employee
* `gender` - employee"s gender
* `dateOfBirth` - employee"s date of birth
* `privatePhone` - employee"s private phone
* `remote` - "1" if potential employee work remotely, "2" if employee work in the office
* `city` - employee"s city
* `street` - employee"s street
* `postalCode` - employee"s postal code
* `privateEmail` - employee"s private email
* `source` - place where employee found offer job
* `trip` - if true, employee work remotely and had organized first day in the office together with a journey, false, if there was no need to organized such day
* `company` - previous employee"s company

