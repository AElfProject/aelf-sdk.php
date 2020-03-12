nce` support.
* Warn users on old gradient direction or flexbox syntax.
* Add `add: false` option to disable new prefixes adding.
* Make Autoprefixer 30% faster.
* Use PostCSS 4.1 plugin API.
* Add prefixes for `pixelated` instead of `crisp-edges` in `image-rendering`.
* Do not add `::placeholder` prefixes for `:placeholder-shown`.
* Fix `text-decoration` prefixes.
* `autoprefixer.process()` was deprecated. Use PostCSS API.

## 5.1.11
* Update `num2fraction` to fix resolution media query (by 一丝).

## 5.1.10
* Do not generate `-webkit-image-rendering`.

## 5.1.9
* Fix DynJS compatibility (by Nick Howes).

## 5.1.8
* Fix gradients in `mask` and `mask-image` properties.
* Fix old webkit prefix on some unsupported gradients.

## 5.1.7
* Fix placeholder selector (by Vincent De Oliveira).

## 5.1.6
* Use official `::placeholder-shown` selector (by Vincent De Oliveira).

## 5.1.5
* Add transition support for CSS Masks properties.

## 5.1.4
* Use `-webkit-` prefix for Opera Mobile 24.

## 5.1.3
* Add IE support for `image-rendering: crisp-edges`.

## 5.1.2
* Add never existed `@-ms-keyframes` to common mistake.

## 5.1.1
* Safer value split in `flex` hack.

## 5.1 “Jianyuan”
* Add support for resolution media query (by 一丝).
* Higher accuracy while removing prefixes in values.
* Add support for logical properties (by 一丝).
* Add `@viewport` support.
* Add `text-overflow` support (by 一丝).
* Add `text-emphasis` support (by 一丝).
* Add `image-rendering: crisp-edges` support.
* Add `text-align-last` support.
* Return `autoprefixer.defaults` as alias to current `browserslist.defaults`.
* Save code style while adding prefixes to `@keyframes` and `@viewport`.
* Do not remove `-webkit-background-clip` with non-spec `text` value.
* Fix `-webkit-filter` in `transition`.
* Better support for browser versions joined on Can I Use
  like `ios_saf 7.0-7.1` (by Vincent De Oliveira).
* Fix compatibility with `postcss-import` (by Jason Kuhrt).
* Fix Flexbox prefixes for BlackBerry and UC Browser.
* Fix gradient prefixes for old Chrome.

## 5.0 “Pravda vítězí”
* Use PostCSS 4.0.
* Use Browserslist to parse browsers queries.
* Use global `browserslist` config.
* Add `> 5% in US` query to select browsers by usage in some country.
* Add `object-fit` and `object-position` properties support.
* Add CSS Shape properties support.
* Fix UC Browser name in debug info.
* Remove `autoprefixer.defaults` and use defaults from Browserslist.

## 4.0.2
* Remove `o-border-radius`, which is common mistake in legacy CSS.

## 4.0.1
* Fix `@supports` support with brackets in values (by Vincent De Oliveira).

## 4.0 “Indivisibiliter ac Inseparabiliter”
* Become 2.5 times fatser by new PostCSS 3.0 parser.
* Do not remove outdated prefixes by `remove: false` option.
* `map.inline` and `map.sourcesContent` options are now `true` by default.
* Add `box-decoration-break` support.
* Do not add old `-webkit-` prefix for gradients with `px` units.
* Use previous source map to show origin source of CSS syntax error.
* Use `from` option from previous source map `file` field.
* Set `to` value to `from` if `to` option is missing.
* Trim Unicode BOM on source maps parsing.
* Parse at-rules without spaces like `@import"file"`.
* Better previous `sourceMappingURL` annotation comment cleaning.
* Do not remove previous `sourceMappingURL` comment on `map.annotation: false`.

## 3.1.2
* Update Firefox ESR version from 24 to 31.

## 3.1.1
* Use Flexbox 2009 spec for Android stock browser < 4.4.

## 3.1 “Satyameva Jayate”
* Do not remove comments from prefixed values (by Eitan Rousso).
* Allow Safari 6.1 to use final Flexbox spec (by John Kreitlow).
* Fix `filter` value in `transition` in Webkits.
* Show greetings if your browsers don’t require any prefixes.
* Add `<=` and `<` browsers requirement (by Andreas Lind).

## 3.0.1
* Fix `autoprefixer.postcss` in callbacks.

## 3.0 “Liberté, Égalité, Fraternité”
* Project was split to autoprefixer (with CLI) and autoprefixer-core.
* `autoprefixer()` now receives only `options` object with `browsers` key.
* GNU format for syntax error messages from PostCSS 2.2.

## 2.2 “Mobilis in mobili”
* Allow to disable Autoprefixer for some rule by control comment.
* Use PostCSS 2.1 with Safe Mode option and broken source line
  in CSS syntax error messages.

## 2.1.1
* Fix `-webkit-background-size` hack for `contain` and `cover` values.
* Don’t add `-webkit-` prefix to `filter` with SVG (by Vincent De Oliveira).

## 2.1 “Eleftheria i thanatos”
* Add support for `clip-path` and `mask` properties.
* Return `-webkit-` prefix to `filter` with SVG URI.

## 2.0.2
* Add readable names for new browsers from 2.0 release.
* Don’t add `-webkit-` prefix to `filter` with SVG URI.
* Don’t add `-o-` prefix 3D transforms.

## 2.0.1
* Save declaration style, when clone declaration to prefix.

## 2.0 “Hongik Ingan”
* Based on PostCSS 1.0.
  See [options changes](https://github.com/postcss/postcss/releases/tag/1.0.0).
* Restore visual cascade after declaration removing.
* Enable visual cascade by default.
* Prefix declareation in `@supports` at-rule conditions.
* Add all browsers from Can I Use: `ie_mob`, `and_chr`, `and_ff`,
  `op_mob` and `op_mini`.
* Allow to use latest Autoprefixer from GitHub by npm.
* Add `--no-cascade`, `--annotation` and `--sources-content` options to binary.

## 1.3.1
* Fix gradient hack, when `background` property contains color.

## 1.3 “Tenka Fubu”
* Add `text-size-adjust` support.
* Add `background-size` to support Android 2.

## 1.2 “Meiji”
* Use Can I Use data from official `caniuse-db` npm package.
* Remove package data update from binary.
* Use increment value instead of current date in minor versions.

## 1.1 “Nutrisco et extingo”
* Add source map annotation comment support.
* Add inline source map support.
* Autodetect previous source map.
* Fix source maps support on Windows.
* Fix source maps support in subdirectory.
* Prefix selector even if it is already prefixed by developer.
* Add option `cascade` to create nice visual cascade of prefixes.
* Fix flexbox support for IE 10 (by Roland Warmerdam).
* Better `break-inside` support.
* Fix prefixing, when two same properties are near.

### 20140222
* Add `touch-action` support.

### 20140226
* Chrome 33 is moved to released versions.
* Add Chrome 36 data.

### 20140302
* Add `text-decoration-*` properties support.
* Update browsers usage statistics.
* Use new PostCSS version.

### 20140319
* Check already prefixed properties after current declaration.
* Normalize spaces before already prefixed check.
* Firefox 28 is moved to released versions.
* Add Firefox 31 data.
* Add some Blackberry data.

### 20140327
* Don’t use `-ms-transform` in `@keyframes`, because IE 9 doesn’t support
  animations.
* Update BlackBerry 10 data.

### 20140403
* Update browsers usage statistics.
* Opera 20 is moved to released versions.
* Add Opera 22 data.

### 20140410
* Chrome 34 is moved to released versions.
* Add Chrome 37 data.
* Fix Chrome 36 data.

### 20140429
* Fix `display: inline-flex` support by 2009 spec.
* Fix old WebKit gradient converter (by Sergey Belov).
* Fix CSS 3 cursors data (by Nick Schonning).

### 20140430
* Separate 2D and 3D transform prefixes to clean unnecessary `-ms-` prefixes.
* Firefox 29 is moved to released versions.
* Add Firefox 32 data.

### 20140510
* Do not add `-ms-` prefix for `transform` with 3D functions.
* Update browsers global usage statistics.

### 20140512
* Remove unnecessary `-moz-` prefix for `wavy` in `text-decoration`.
* Update Safari data for font properties.

### 20140521
* Chrome 36 is moved to released versions.
* Add Chrome 38 data.

### 20140523
* Opera 21 is moved to released versions.
* Add Opera 23 data.

### 20140605
* Allow to parse gradients without space between color and position.
* Add iOS 8, Safari 8 and Android 4.4.3 data.
* Update browsers usage statistics.

## 1.0 “Plus ultra”
* Source map support.
* Save origin indents and code formatting.
* Change CSS parser to PostCSS.
* Preserve vendor-prefixed properties put right after unprefixed ones.
* Rename `compile()` to `process()` Changelog
=========

## UNRELEASED

## 1.7.0 (2020-02-14)

### Added

* added `Assert::notFalse()`
* added `Assert::isAOf()`
* added `Assert::isAnyOf()`
* added `Assert::isNotA()`

## 1.6.0 (2019-11-24)

### Added

* added `Assert::validArrayKey()`
* added `Assert::isNonEmptyList()`
* added `Assert::isNonEmptyMap()`
* added `@throws InvalidArgumentException` annotations to all methods that throw.
* added `@psalm-assert` for the list type to the `isList` assertion.

### Fixed

* `ResourceBundle` & `SimpleXMLElement` now pass the `isCountable` assertions.
They are countable, without implementing the `Countable` interface.
* The doc block of `range` now has the proper variables.
* An empty array will now pass `isList` and `isMap`. As it is a valid form of both.
If a non empty variant is needed, use `isNonEmptyList` or `isNonEmptyMap`.

