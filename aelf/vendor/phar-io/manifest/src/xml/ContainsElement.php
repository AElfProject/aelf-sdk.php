'*'],
    feature: 'calc',
    browsers: browsers
  });
}); // Background options

f(require('caniuse-lite/data/features/background-img-opts'), function (browsers) {
  return prefix(['background-origin', 'background-size'], {
    feature: 'background-img-opts',
    browsers: browsers
  });
}); // background-clip: text

f(require('caniuse-lite/data/features/background-clip-text'), function (browsers) {
  return prefix(['background-clip'], {
    feature: 'background-clip-text',
    browsers: browsers
  });
}); // Font feature settings

f(require('caniuse-lite/data/features/font-feature'), function (browsers) {
  return prefix(['font-feature-settings', 'font-variant-ligatures', 'font-language-override'], {
    feature: 'font-feature',
    browsers: browsers
  });
}); // CSS font-kerning property

f(