# Apache ECharts for MediaWiki

Apache Echarts is a powerful charting environment. This extension allows to make full use of it in your MediaWiki setups.

## Installation

Copy the `ECharts` folder in the `extensions` folder of your mediawiki installation.

Add `wfLoadExtension( 'ECharts' );` in your `LocalSettings.php`

## Usage
Build your chart in the [Apache Echarts editor](https://echarts.apache.org/examples/en/editor.html?c=bar-stack), then copy the code and paste it in your wiki page, surrounding with `{{#echarts: yourcode }}`

NB : your code should only contain the `option = { ... }` declaration. It should not contain constants or other functions declarations.

Also, make sure your code does not contain double curly braces `{{` or `}}` (just add spaces between them), otherwise mediawiki will be very confused about where does the echarts parser function start and end.

## Size and alignment
By default your charts will have a 100% width and 400px height. You can customize that by using:

    {{#echarts:
    width=300px |
    height=300px |
    yourcode }}

You can also add an alignment setting in order to float right or left (will only work if you have bootstrap in your skin):

    {{#echarts:
    width=300px |
    height=300px |
    align=right |
    yourcode }}

## Important warning - caveat
This extension will analyse the ECharts code by using the JS function eval. This might allow malicious code to get into your wiki pages. You don't want that. The only other option is to use JSON.parse(...) but the Echarts code is not strict Json so that would fail unless you rework your JSON data using a JSON beautifier/linter of some sort. If your wiki is opened to anonymous contributions, don't use this extension. You've been warned! (NB: this could be configured using a configuration option - PR welcome)
