# POST /api/tribe/{id}/disconnectSlack

Disconnects tribe from Slack integration.

```
curl --request POST \
  --url 'URL/api/tribe/1/disconnectSlack \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_TRIBE_MASTER` role or above.

## Result
Empty JSON object.