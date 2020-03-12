n !check.test(all);
  }
  /**
     * Return true if we need to remove node
     */
  ;

  _proto.toRemove = function toRemove(str, all) {
    var _this$parse2 = this.parse(str),
        prop = _this$parse2[0],
        value = _this$parse2[1];

    var unprefixed = this.all.unprefixed(prop);
    var cleaner = this.all.cleaner();

    if (cleaner.remove[prop] && cleaner.remove[prop].remove && !this.isHack(all, unprefixed)) {
      return true;
    }

    for (var _iterator3 = cleaner.values('remove', unprefixed), _isArray3 = Array.isArray(_iterator3), _i3 = 0, _iterator3 = _isArray3 ? _iterator3 : _iterator3[Symbol.iterator]();;) {
      var _ref3;

      if (_isArray3) {
        if (_i3 >= _iterator3.length) break;
        _ref3 = _iterator3[_i3++];
      } else {
        _i3 = _iterator3.next();
        if (_i3.done) break;
        _ref3 = _i3.value;
      }

      var checker = _ref3;

      if (checker.check(value)) {
        return true;
      }
    }

    return false;
  }
  /**
     * Remove all unnecessary prefixes
     */
  ;

  _proto.remove = function remove(nodes, all) {
    var i = 0;

    while (i < nodes.length) {
      if (!this.isNot(nodes[i - 1]) && this.isProp(nodes[i]) && this.isOr(nodes[i + 1])) {
        if (this.toRemove(nodes[i][0], all)) {
          nodes.splice(i, 2);
          continue;
        }

        i += 2;
        continue;
      }

      if (typeof nodes[i] === 'object') {
        nodes[i] = this.remove(nodes[i], all);
      }

      i += 1;
    }

    return nodes