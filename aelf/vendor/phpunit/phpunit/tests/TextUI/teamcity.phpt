"use strict";

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var parser = require('postcss-value-parser');

var vendor = require('postcss').vendor;

var list = require('postcss').list;

var Transition =
/*#__PURE__*/
function () {
  function Transition(prefixes) {
    _defineProperty(this, "props", ['transition', 'transition-property']);

    this.prefixes = prefixes;
  }
  /**
   * Process transition and add prefixes for all necessary properties
   */


  var _proto = Transition.prototype;

  _proto.add = function add(decl, result) {
    var _this = this;

    var prefix, prop;
    var add = this.prefixes.add[decl.prop];
    var declPrefixes = add && add.prefixes || [];
    var params = this.parse(decl.value);
    var names = params.map(function (i) {
      return _this.findProp(i);
    });
    var added = [];

    if (names.some(function (i) {
      return i[0] === '-';
    })) {
      return;
    }

    for (var _iterator = params, _isArray = Array.isArray(_iterator), _i = 0, _iterator = _isArray ? _iterator : _iterator[Symbol.iterator]();;) {
      var _ref;

      if (_isArray) {
        if (_i >= _iterator.length) break;
        _ref = _iterator[_i++];
      } else {
        _i = _iterator.next();
        if (_i.done) break;
        _ref = _i.value;
      }

      var param = _ref;
      prop = this.findProp(param);
      if (prop[0] === '-') continue;
     