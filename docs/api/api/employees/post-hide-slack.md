# POST /api/employees/hideSlack

Disables Slack integration for current user.

```
curl --request POST \
  --url 'URL/api/employees/hideSlack' \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_USER` role or above

### Result
Empty JSON object.
