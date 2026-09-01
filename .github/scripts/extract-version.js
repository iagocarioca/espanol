#!/usr/bin/env node

'use strict';

const fs = require('fs');

const DEFAULT_VERSION = '1.0.0';
const DEFAULT_CANDIDATES = [
  'espanol/style.css',
  'style.css',
];

function extractVersionFromText(text) {
  const match = text.match(/^[\s*]*Version:\s*([0-9]+(?:\.[0-9]+){1,3})\s*$/im);
  return match ? match[1].trim() : '';
}

function extractVersion(candidates = DEFAULT_CANDIDATES) {
  for (const candidate of candidates) {
    if (!fs.existsSync(candidate)) continue;

    const version = extractVersionFromText(fs.readFileSync(candidate, 'utf8'));

    if (version) {
      return {
        source: candidate,
        version,
      };
    }
  }

  return {
    source: '',
    version: DEFAULT_VERSION,
  };
}

if (require.main === module) {
  const candidates = process.argv.slice(2);
  const result = extractVersion(candidates.length > 0 ? candidates : DEFAULT_CANDIDATES);

  if (result.source) {
    console.error(`Version source: ${result.source}`);
  } else {
    console.error(`Version not found; using fallback ${DEFAULT_VERSION}`);
  }

  console.log(result.version);
}

module.exports = {
  DEFAULT_CANDIDATES,
  DEFAULT_VERSION,
  extractVersion,
  extractVersionFromText,
};
