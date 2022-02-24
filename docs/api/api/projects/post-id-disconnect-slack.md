# POST /api/projects/{id}/disconnectSlack

Disables project integration with Slack.

```
curl --request POST \
  --url 'URL/api/projects/15/disconnectSlack\
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_MANAGER` role or above.

## Result
Empty JSON object.
