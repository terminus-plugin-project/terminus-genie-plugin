# Terminus Genie Plugin

[![Terminus v1.x Compatible](https://img.shields.io/badge/terminus-v1.x-green.svg)](https://github.com/terminus-plugin-project/terminus-genie-plugin/tree/1.x)

Terminus plugin to run a terminus command against multiple sites.

Ever wanted to do something to all of your Pantheon sites?  Maybe you want to 
add a team member, change your upstream, or even run a Drush or WP-CLI command 
against all of them.  This plugin allows you to do this without needing to run
a command against each site individually or writing your own scripts.

## Examples
### Get info about all sites.
```
$ terminus genie -- site:info
```

### Get info about all sites owned by the current user.
```
$ terminus genie --owner=me -- site:info
```

### Lock a particular environment on all sites
```
$ terminus genie --env=dev -- lock:enable username password
```

## Installation
For help installing, see [Manage Plugins](https://pantheon.io/docs/terminus/plugins/)
```
mkdir -p ~/.terminus/plugins
composer create-project -d ~/.terminus/plugins terminus-plugin-project/terminus-genie-plugin:~1
```

## Help
Run `terminus list site:update` for a complete list of available commands. Use `terminus help <command>` to get help on one command.
