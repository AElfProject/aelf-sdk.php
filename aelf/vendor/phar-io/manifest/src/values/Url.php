ition'],
    feature: 'css-sticky',
    browsers: browsers
  });
}); // Pointer Events

f(require('caniuse-lite/data/features/pointer'), function (browsers) {
  return prefix(['touch-action'], {
    feature: 'pointer',
    browsers: browsers
  });
}); // Text decoration

var decoration = require('caniuse-lite/data/features/text-decoration');

f(decoration, function (browsers) {
  return prefix(['text-decoration-style', 'text-decoration-color', 'text-decoration-line', 'text-decoration'], {
    feature: 'text-decoration',
    browsers: browsers
  });
});
f(decoration, {
  match: /x.*#[235]/
}, function (browsers) {
  return prefix(['text-decoration-skip', 'text-decoration-skip-ink'], {
    feature: 'text-decoration',
    browsers: browsers
  });
}); // Text Size Adjust

f(require('caniuse-lite/data/features/text-size-adjust'), function (browsers) {
  return prefix(['text-size-adjust'], {
    feature: 'text-size-adjust',
    browsers: browsers
  });
}); // 