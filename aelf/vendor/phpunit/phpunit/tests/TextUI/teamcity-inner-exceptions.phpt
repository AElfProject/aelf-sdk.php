lue) {
    return decl.parent.some(function (i) {
      return i.prop === prop && i.value === value;
    });
  }
  /**
   * Add declaration if it is not exist
   */
  ;

  _proto.cloneBefore = function cloneBefore(decl, prop, value) {
    if (!this.already(decl, prop, value)) {
      decl.cloneBefore({
        prop: prop,
        value: value
      });
    }
  }
  /**
   * Show transition-property warning
   */
  ;

  _proto.checkForWarning = function checkForWarning(result, decl) {
    if (decl.prop !== 'transition-property') {
      return;
    }

    decl.parent.each(function (i) {
      if (i.type !== 'decl') {
        return undefined;
      }

      if (i.prop.indexOf('transition-') !== 0) {
        return undefined;
      }

      if (i.prop === 'transition-property') {
        return undefined;
      }

      if (list.comma(i.value).length > 1) {
        decl.warn(result, 'Replace transition-property to transition, ' + 'because Autoprefixer could not support ' + 'any cases of transition-property ' + 'and other transition-*');
      }

      return false;
    });
  }
  /**
   * Process transition and remove all unnecessary properties
   */
  ;

  _proto.remove = function remove(decl) {
    var _this2 = this;

    var params = this.parse(decl.value);
    params = params.filter(function (i) {
      var prop = _this2.prefixes.remove[_this2.findProp(i)];

      return !prop || !prop.remove;
    });
    var value = this.stringify(params);

    if (decl.value === value) {
      return;
    }

    if (params.length === 0) {
      decl.remove();
      return;
    }

    var double = decl.parent.some(function (i) {
      return i.prop === decl.prop && i.value === value;
    });
    var smaller = decl.parent.some(function (i) {
      return i !== decl && i.prop === decl.prop && i.value.length > value.length;
    });

    if (double || smaller) {
      decl.remove();
      return;
    }

    decl.value = value;
  }
  /**
   * Parse properties list to array
   */
  ;

  _proto.parse = function parse(value) {
    var ast = parser(value);
    var result = [];
  