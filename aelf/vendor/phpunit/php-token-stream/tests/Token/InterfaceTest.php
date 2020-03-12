 duplicates and not inside media rule
        // and the selector is complex
        gridAreaRule.walkDecls(/-ms-grid-(row|column)/, function (d) {
          return d.remove();
        });
        getMSDecls(area, area.row.updateSpan, area.column.updateSpan).reverse().forEach(function (i) {
          return gridAreaRule.prepend(Object.assign(i, {
            raws: {
              between: gridArea.raws.between
            }
          }));
        });
      } else if (rule.params) {
        (function () {
          // grid-template is inside media rule
          // if we're inside media rule, we need to store prefixed rules
          // inside rulesToInsert object to be able to preserve the order of media
          // rules and merge them easily
          var cloned = gridAreaRule.clone();
          cloned.removeAll();
          getMSDecls(area, area.row.updateSpan, area.column.updateSpan).reverse().forEach(function (i) {
            return cloned.prepend(Object.assign(i, {
              raws: {
                between: gridArea.raws.between
              }
            }));
          });

          if (rule.hasDuplicates && hasDuplicateName) {
            cloned.selectors = changeDuplicateAreaSelectors(cloned.selectors, rule.selectors);
          }

          cloned.raws = rule.node.raws;

          if (css.index(rule.node.parent) > gridAreaRuleIndex) {
            // append the prefixed rules right inside media rule
            // with grid-template
            rule.node.parent.append(cloned);
          } else {
            // store the rule to insert later
            rulesToInsert[lastArea][rule.params].push(cloned);
          } // set new rule as last rule ONLY if we didn't set lastRule for
          // this grid-area before


          if (!lastRuleIsSet) {
            rulesToInsert[lastArea].lastRule = gridAreaMedia || gridAreaRule;
          }
        })();
      }
    }

    return undefined;
  }); // append stored rules inside the media rules

  Object.keys(rulesToInsert).forEach(function (area) {
    var data = rulesToInsert[area];
    var lastRule = data.lastRule;
    Object.keys(data).reverse().filter(function (p) {
      return p !== 'lastRule';
    }).forEach(function (params) {
      if (data[params].length > 0 && lastRule) {
        lastRule.after({
          name: 'media',
          params: params
        });
        lastRule.next().append(data[params]);
      }
    });
  });
  return undefined;
}
/**
 * Warn user if grid area identifiers are not found
 * @param  {Object} areas
 * @param  {Declaration} decl
 * @param  {Result} result
 * @return {void}
 */


function warnMissedAreas(areas, decl, result) {
  var missed = Object.keys(areas);
  decl.root().walkDecls('grid-area', function (gridArea) {
    missed = missed.filter(function (e) {
      return e !== gridArea.value;
    });
  });

  if (missed.length > 0) {
    decl.warn(result, 'Can not find grid areas: ' + missed.join(', '));
  }

  return undefined;
}
/**
 * compare selectors with grid-area rule and grid-template rule
 * show warning if grid-template selector is not found
 * (this function used for grid-area rule)
 * @param  {Declaration} decl
 * @param  {Result} result
 * @return {void}
 */


function warnTemplateSelectorNotFound(decl, result) {
  var rule = decl.parent;
  var root = decl.root();
  var duplicatesFound = false; // slice selector array. Remove the last part (for comparison)

  var slicedSelectorArr = list.space(rule.selector).filter(function (str) {
    return str !== '>';
  }).slice(0, -1); // we need to compare only if selector is complex.
  // e.g '.grid-cell' is simple, but '.parent > .grid-cell' is complex

  if (slicedSelectorArr.length > 0) {
    var gridTemplateFound = false;
    var foundAreaSelector = null;
    root.walkDecls(/grid-template(-areas)?$/, function (d) {
      var parent = d.parent;
      var templateSelectors = parent.selectors;

      var _parseTemplate2 = parseTemplate({
        decl: d,
        gap: getGridGap(d)
      }),
          areas = _parseTemplate2.areas;

      var hasArea = areas[decl.value]; // find the the matching selectors

      for (var _iterator3 = templateSelectors, _isArray3 = Array.isArray(_iterator3), _i3 = 0, _iterator3 = _isArray3 ? _iterator3 : _iterator3[Symbol.iterator]();;) {
        var _ref10;

        if (_isArray3) {
          if (_i3 >= _iterator3.length) break;
          _ref10 = _iterator3[_i3++];
        } else {
          _i3 = _iterator3.next();
          if (_i3.done) break;
          _ref10 = _i3.value;
        }

        var tplSelector = _ref10;

        if (gridTemplateFound) {
          break;
        }

        var tplSelectorArr = list.space(tplSelector).filter(function (str) {
          return str !== '>';
        });
        gridTemplateFound = tplSelectorArr.every(function (item, idx) {
          return item === slicedSelectorArr[idx];
        });
      }

      if (gridTemplateFound || !hasArea) {
        return true;
      }

      if (!foundAreaSelector) {
        foundAreaSelector = parent.selector;
      } // if we found the duplicate area with different selector


      if (foundAreaSelector && foundAreaSelector !== parent.selector) {
        duplicatesFound = true;
      }

      return undefined;
    }); // warn user if we didn't find template

    if (!gridTemplateFound && duplicatesFound) {
      decl.warn(result, "Autoprefixer cannot find a grid-template " + ("containing the duplicate grid