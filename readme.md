Plugin for YOURLS 1.6+: Custom API Action

# Description

Request clicks for a `shorturl` (required) between `since` (optional) and `until` (optional) using something like the following:
`https://sho.rt/yourls-api.php?username=xxxxx&password=yyyyy&format=json&action=url-stats-period&shorturl=abc&since=1540234300&until=1540234309` 

# Return value

The API function returns something like this:
```json
	{"statusCode":200,"message":"success","url-stats-period":{"clicks":"1"}}
```
