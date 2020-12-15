# Ignis Numbered Pagination
Ignis Numbered Pagination plugin: enables numbered pagination on aTheme's Ignis WordPress theme. No settings, just install and activate the plugin.

## Installation 

1. Get the .zip package of this plugin
2. Install manually to your WordPress website from **Plugins** menu > **Add New** > Upload
3. Activate and the numbered pagination is set instantly, no extra settings required 

## Styling 

Copy the CSS code below to your website's **Additional CSS** and make some changes that you need.

```
.ingnis-numerred-nav.paging-navigation {
  margin: 48px 0;
}

.ingnis-numerred-nav.paging-navigation .loop-pagination {
  margin-top: -5px;
  text-align: center;
}

.ingnis-numerred-nav.paging-navigation .page-numbers {
  border-top: 5px solid transparent;
  display: inline-block;
  font-size: 14px;
  font-weight: 900;
  margin-right: 1px;
  padding: 7px 16px;
  text-transform: uppercase;
}

.ingnis-numerred-nav.paging-navigation a:hover {
  color: inherit;
}

.ingnis-numerred-nav.paging-navigation .page-numbers.current {
  background-color: #ff6b7e;
  color: #fff;
}

.ingnis-numerred-nav.paging-navigation a:hover {
  background-color: transparent;
}
``` 

## Credit 

This plugin is based on [sudipbd's gist](https://gist.github.com/sudipbd/45cca73a78953b69fdbcd160e6430905).

## License 

GPL v2.
