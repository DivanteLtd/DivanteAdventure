# PATCH /api/projects/hide/{id}

Marks project as hidden.

```
curl --request PATCH \
  --url 'URL/api/projects/hide/15\
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_MANAGER` role or above.

## Result
Empty JSON object.
