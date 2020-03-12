and return result object,
  instead of CSS string.
* Rename `inspect()` to `info()`.
* Add in binary `-d` option to specify output directory.
* Binary now will not concat output files.
* Allow to select last versions for specified browser.
* Add full browser names aliases: `firefox`, `explorer` and `blackberry`.
* Ignore case in browser names.
* Change license to MIT.
* Add prefixes inside custom at-rules.
* Add only necessary prefixes to selector inside prefixed at-rule.
* Safer backgrounds list parser in gradient hack.
* Prefix `@keyframes` inside `@media`.
* Don’t prefix values for CSS3 PIE properties.
* Binary now shows file name in syntax error.
* Use browserify to build standalone version.

### 20131225
* Fix deprecated API convertor.
* Add `::placeholder` support for Firefix >= 18.
* Fix vendor prefixes order.

### 20140103
* Add `-webkit-` prefix for `sticky` position.
* Update browsers popularity statistics.

### 20140109
* Add selectors and at-rules sections to debug info.
* Fix outdated prefixes cleaning.

### 20140110
* Add `Firefox ESR` browser requirement.
* Opera 18 is moved to released versions.
* Add Opera 20 data.

### 20140117
* Chrome 32 is moved to released versions.
* Add Opera 34 data.

### 20140130
* Fix flexbox properties names in transitions.
* Add Chrome 35 and Firefox 29 data.

### 20140203
* Android 4.4 stock browser and Opera 19 are moved to released versions.
* Add Opera 21 data.
* Update browsers usage statistics.

### 20140213
* Add case insensitive to IE’s filter hack (by Dominik Schilling).
* Improve selector prefixing in some rare cases (by Simon Lydell).
* Firefox 27 is moved to released versions.
* Add Firefox 30 data.

## 0.8 “Unbowed, Unbent, Unbroken”
* Add more browsers to defaults ("> 1%, last 2 versions, ff 17, opera 12.1"
  instead of just "last 2 browsers").
* Keep vendor prefixes without unprefixed version (like vendor-specific hacks).
* Convert gradients to old WebKit syntax (actual for Android 2.3).
* Better support for several syntaxes with one prefix (like Flexbox and
  gradients in WebKit).
* Add intrinsic and extrinsic sizing values support.
* Remove never existed prefixes from common mistakes (like -ms-transition).
* Add Opera 17 data.
* Fix selector prefixes order.
* Fix browser versions order in inspect.

### 20130903
* Fix old WebKit gradients convertor on rgba() colors.
* Allow to write old direction syntax in gradients.

### 20130906
* Fix direction syntax in radial gradients.
* Don’t prefix IE filter with modern syntax.

### 20130911
* Fix parsing property name with spaces.

### 20130919
* Fix processing custom framework prefixes (by Johannes J. Schmidt).
* Concat outputs if several files compiled to one output.
* Decrease standalone build size by removing unnecessary Binary class.
* iOS 7 is moved to re