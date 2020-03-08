#!/usr/bin/env sh

cd "$(git rev-parse --show-toplevel 2>/dev/null)" || exit 2;

echo 'Checking for PHP syntax errors';
if ! find . -type f -name "*.php" -and ! -wholename "*/vendor/*" -print0 2>/dev/null | xargs -0I{} -n1 php -l {} >/dev/null; then
  exit 2;
fi

echo 'Checking for strict typed classes';
nonStrictFiles=$(find . -type f -name "*.php" -and ! -wholename "*/vendor/*" -print0 2>/dev/null | xargs -0I{} -n1 grep -L 'declare(strict_types=1);' {} | xargs -I{} echo ' -{}');
if [ -n "${nonStrictFiles}" ]; then
  echo "${nonStrictFiles}" >&2;
  exit 3;
fi

echo 'Checking console';
if ! ./bin/console.php '\Fugue\Command\TestCommand'; then
    echo 'Console test failed!' >&2;
    exit 4;
fi
