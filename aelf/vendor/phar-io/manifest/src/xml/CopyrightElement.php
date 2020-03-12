ts, {
  match: /y\sx/
}, function (browsers) {
  return prefix(['linear-gradient', 'repeating-linear-gradient', 'radial-gradient', 'repeating-radial-gradient'], {
    props: ['background', 'background-image', 'border-image', 'mask', 'list-style', 'list-style-image', 'content', 'mask-image'],
    mistakes: ['-ms-'],
    feature: 'css-gradients',
    browsers: browsers
  });
});
f(gradients, {
  match: /a\sx/
}, function (browsers) {
  browsers = browsers.map(function (i) {
    if (/firefox|op/.test(i)) {
      return i;
    } else {
      return i + " old";
    }
  });
  return add(['linear-gradient', 'repeating-linear-gradient', 'radial-gradient', 'repeating-radial-gradient'