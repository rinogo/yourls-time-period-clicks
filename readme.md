Plugin for YOURLS 1.6+: Custom API Action

# Description
Request clicks for a `url` or `shorturl` (required) between `since` (optional) and `until` (optional) using something like the following:
`https://sho.rt/yourls-api.php?username=xxxxx&password=yyyyy&format=json&action=url-stats-period&shorturl=abc&since=1540234300&until=1540234309` 

# Return value
The API function returns something like this:
```json
	{"statusCode":200,"message":"success","url-stats-period":{"clicks":"1"}}
```

# Installation
1. In `/user/plugins`, create a new folder named `time-period-clicks`.
2. Drop these files in that directory.
3. Go to the Plugins administration page ( *eg* `http://sho.rt/admin/plugins.php` ) and activate the plugin.
4. Have fun!

# License
Released under the [MIT License](https://opensource.org/licenses/MIT).
