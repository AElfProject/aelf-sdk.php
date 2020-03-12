on:
  push:
    branches:
      - master
  pull_request:
name: Qa workflow
jobs:
  setup:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@master
      - name: Restore/cache vendor folder
        uses: actions/cache@v1
        with:
          path: vendor
          key: all-build-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            all-build-${{ hashFiles('**/composer.lock') }}
            all-build-
      - name: Restore/cache tools folder
        uses: actions/cache@v1
        with:
          path: tools
          key: all-tools-${{ github.sha }}
          restore-keys: |
            all-tools-${{ github.sha }}-
            all-tools-
      - name: composer
        uses: docker://composer
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_T