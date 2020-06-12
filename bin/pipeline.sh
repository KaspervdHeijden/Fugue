#!/usr/bin/env sh

dirName=$(git rev-parse --show-toplevel 2>/dev/null);
if [ -z "${dirName}" ] || [ ! -d "${dirName}" ]; then
  echo 'Not a git repository' >&2;
  exit 5;
fi

echo 'Checking for PHP syntax errors...';

phpFiles=$(find "${dirName}" -type f -name '*.php' -and ! -wholename '*/vendor/*' 2>/dev/null);
output=$(echo "${phpFiles}" | xargs -I{} php -l {} | grep 'Errors parsing ' | cut -c16-);
if [ -n "${output}" ]; then
  echo "${output}" | xargs -I{} echo ' - {}' >&2;
  exit 6;
fi

echo 'Checking for strict typed classes...';
output=$(echo "${phpFiles}" | xargs -I{} -n1 grep -L 'declare(strict_types=1);' {} | xargs -I{} echo ' - {}');
if [ -n "${output}" ]; then
  echo "${output}" >&2;
  exit 7;
fi

echo 'Checking console...';
if ! "${dirName}/bin/console" '\Fugue\Command\TestCommand'; then
  echo 'Console test failed!' >&2;
  exit 8;
fi
