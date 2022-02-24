# PATCH /api/tribe/{id}

Updates existing tribe.

```
curl --request POST \
  --url 'URL/api/tribe/1 \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
	"name": "Super tribe"
  }'
```

### Security requirements
`ROLE_TRIBE_MASTER` role or above.

## Request
```json
{
  "name": "Super tribe",
  "description": "Super tribe description",
  "photoUrl": "https://sample.tld/photo.png",
  "url": "https://super-tribe-page.sample.tld",
  "isVirtual": false,
  "responsible": [23, 45]
}
```

* `name` - Tribe's name
* `photoUrl` - URL to tribe's photo
* `url` - URL address to show as tribe page
* `isVirtual` - if `true`, tribe cannot have any projects
* `description` - Tribe's description
* `responsible` - Tribe's responsible employees id

## Result
Empty JSON object.