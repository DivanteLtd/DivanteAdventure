# POST /api/potential_employee

Creates new potential employee.

```
curl --request POST \
  --url 'URL/api/potential_employee \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
    "name": "John"
  }'
```

### Security requirements
`ROLE_HR` role or above.

## Request
JSON object representing one entry.
        
```json
{
  "name": "John",
  "lastName": "Smith",
  "email": "jsmith@example.com",
  "tribeId": 1,
  "positionId": 15,
  "hireDate": "2010-12-31",
  "gender": 1,
  "dateOfBirth": "2010-12-31",
  "privatePhone": 123456789,
  "remote": true,
  "city": "Wroclaw",   "postalCode": "50-231",
  "street": "Kosciuszki",   "privateEmail": "jsmith@example.com",
  "source": "company website",
  "trip": true,
  "company": "SoftwareHouse 123"
}
```

* `name` - employee's name
* `lastName` - employee's last name
* `email` - suggested email for a potential employee
* `tribeId` - Id of employee's potential tribe
* `positionId` - ID of employee's potential position
* `hireDate` - date in YYYY-MM-DD format when employee was or will be hired
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

## Result
Empty JSON object.
