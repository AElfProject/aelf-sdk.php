ndor folder
        uses: actions/cache@v1
        with:
          path: vendor
          key: all-build-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            all-build-${{ hashFiles('**/composer.lock') }}
            all-build-
      - name: Code style check
        uses: phpDocumentor/coding-standard@master
        with:
          args: -s

  phpstan:
    runs-on: ubuntu-latest
    needs: [setup, phpunit]
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
      - name: PHPStan
        uses: phpDocumentor/phpstan-ga@master
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
        with:
          args: analyse src --configuration phpstan.neon

  psalm:
    runs-on: ubuntu-latest
    needs: [setup, phpunit]
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
          key: all-t