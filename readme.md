# Repository Note 

This server component is used for the Update Check in Codiad. There is no requirement to download or install.

# Manual Update

- For a manual update of your system, you could use the prepared ```upgrade.sh``` in your root directory.
- Assign execution permission to it first

```
chmod 744 upgrade.sh
./upgrade.sh <installation directory> <webserver user>
```

Example Apache2:
```
./upgrade.sh /var/www/ www-data
```

# Automatic Update

- Use crontab for this. Updates are applied automatically to the system and will be checked at midnight once a day.

```
chmod 744 upgrade.sh
crontab -e
```

and add

```
0 0 * * * <installation directory>/upgrade.sh <installation directory> <webserver user>
```

Example Apache2:
```
0 0 * * * /var/www/upgrade.sh /var/www/ www-data
```

# Codiad Web IDE

Codiad is a web-based IDE framework with a small footprint and minimal requirements. The system is still early in development, and while it has been proven extremely stable please be sure have a backup system if you use it in any production work.

Keep up to date with the latest changes and news on **[Twitter](http://twitter.com/codiadide)** or **[Facebook](http://www.facebook.com/Codiad)**

For more information on the project please check out the **[check out the Wiki](https://github.com/Codiad/Codiad/wiki)** or **[the Codiad Website](http://www.codiad.com)**

Distributed under the MIT-Style License. See `LICENSE.txt` file for more information.
