#!/usr/bin/env sh

dirName=$(git rev-parse --show-toplevel 2>/dev/null);
if [ -z "${dirName}" ]; then
  echo 'Not a git repository' >&2;
  exit 5;
fi

echo 'Checking for PHP syntax errors...';
output=$(find "${dirName}" -type f -name '*.php' -and ! -wholename '*/vendor/*' -exec php -l {} \; 2>&1 | grep 'Errors parsing ' | cut -c16-);
if [ -n "${output}" ]; then
  echo "${output}" | xargs -I{} echo ' - {}' >&2;
  exit 6;
fi

echo 'Checking for strict typed classes...';
output=$(find "${dirName}" -type f -name '*.php' -and ! -wholename '*/vendor/*' -print0 2>/dev/null | xargs -0I{} -n1 grep -L 'declare(strict_types=1);' {} | xargs -I{} echo ' - {}');
if [ -n "${output}" ]; then
  echo "${output}" >&2;
  exit 7;
fi

echo 'Checking console...';
if ! "${dirName}/bin/console.php" '\Fugue\Command\TestCommand'; then
  echo 'Console test failed!' >&2;
  exit 8;
fi
