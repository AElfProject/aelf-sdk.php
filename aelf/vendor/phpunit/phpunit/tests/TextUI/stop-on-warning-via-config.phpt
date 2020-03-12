   if (_isArray6) {
        if (_i6 >= _iterator6.length) break;
        _ref4 = _iterator6[_i6++];
      } else {
        _i6 = _iterator6.next();
        if (_i6.done) break;
        _ref4 = _i6.value;
      }

      var i = _ref4;

      if (!changed && i.type === 'word' && i.value === origin) {
        result.push({
          type: 'word',
          value: name
        });
        changed = true;
      } else {
        result.push(i);
      }
    }

    return result;
  }
  /**
   * Find or create separator
   */
  ;

  _proto.div = function div(params) {
    for (var _iterator7 = params, _isArray7 = Array.isArray(_iterator7), _i7 = 0, _iterator7 = _isArray7 ? _iterator7 : _iterator7[Symbol.iterator]()