"use strict";

/* eslint-disable import-helpers/order-imports */
var unpack = require('caniuse-lite').feature;

function browsersSort(a, b) {
  a = a.split(' ');
  b = b.split(' ');

  if (a[0] > b[0]) {
    return 1;
  } else if (a[0] < b[0]) {
    return -1;
  } else {
    return Math.sign(parseFloat(a[1]) - parseFloat(b[1]));
  }
} // Convert Can I Use data


function f(data, opts, callback) {
  data = unpack(data);

  if (!callback) {
    var _ref = [opts, {}];
    callback = _ref[0];
    opts = _ref[1];
  }

  var match = opts.match || /\sx($|\s)/;
  var need = [];

  for (var browser in data.stats) {
    var versions = data.stats[browser];

    for (var version in versions) {
      var support = versions[version];

      if (support.match(match)) {
        need.push(browser + ' ' + version);
      }
    }
  }

  callback(need.sort(browsersSort));
} // Add data for all properties


var result = {};

function prefix(names, data) {
  for (var _iterator = names, _isArray = Array.isArray(_iterator), _i = 0, _iterator = _isArray ? _iterator : _iterator[Symbol.iterator]();;) {
    var _ref2;

    if (_isArray) {
      if (_i >= _iterator.length) break;
      _ref2 = _iterator[_i++];
    } else {
      _i = _iter