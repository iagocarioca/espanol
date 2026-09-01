'use strict';

const assert = require('node:assert/strict');
const { test } = require('node:test');

const { buildPayload, buildSummary, categorizeCommit } = require('./generate-changelog-discord');

const baseGitInfo = {
  commitHash: 'abc1234',
  commitFull: 'abc1234567890',
  commitMsg: 'feat: melhora a notificação do deploy',
  commitAuthor: 'Rhuan',
  commitDate: '2026-07-02T12:00:00Z',
  fileChanges: [
    { status: 'A', file: '.github/scripts/novo.js' },
    { status: 'M', file: '.github/scripts/generate-changelog-discord.js' },
    { status: 'D', file: '.github/scripts/antigo.js' },
  ],
};

test('buildPayload monta a mensagem visual sem seção de IA', () => {
  const payload = buildPayload({
    version: '1.2.3',
    status: 'success',
    serverUrl: 'https://github.com',
    repository: '085-Digital/porno-mineiro-wp',
    sha: 'abc1234567890',
    runId: '123',
    actor: 'rhuan',
    branch: 'develop',
    gitInfo: baseGitInfo,
  });

  const fieldNames = payload.embeds[0].fields.map((field) => field.name);

  // A funcionalidade de IA foi removida.
  assert.ok(!fieldNames.some((name) => name.includes('Detalhes das alterações')));

  // "Por que mudou" não deve mais aparecer em nenhum campo.
  const allText = payload.embeds[0].fields.map((field) => field.value).join('\n');
  assert.doesNotMatch(allText, /Por que mudou/);

  // Campos visuais esperados.
  assert.ok(fieldNames.some((name) => name.includes('Resumo')));
  assert.ok(fieldNames.some((name) => name.includes('Arquivos alterados')));
  assert.ok(fieldNames.some((name) => name.includes('Links')));

  // Categoria com emoji na descrição.
  assert.match(payload.embeds[0].description, /✨ \*\*Feature:\*\*/);
  assert.match(payload.content, /Branch:/);
  assert.match(payload.embeds[0].title, /✅/);
});

test('buildSummary lista versão, branch, autor, commit e estatística de arquivos', () => {
  const summary = buildSummary({
    version: '1.2.3',
    branch: 'main',
    actor: 'rhuan',
    commitHash: 'abc1234',
    sha: 'abc1234567890',
    serverUrl: 'https://github.com',
    repository: '085-Digital/porno-mineiro-wp',
    fileChanges: baseGitInfo.fileChanges,
  });

  assert.match(summary, /Versão:\*\* `1.2.3`/);
  assert.match(summary, /Branch:\*\* `main`/);
  assert.match(summary, /Autor:\*\* rhuan/);
  assert.match(summary, /🟢 1 add/);
  assert.match(summary, /🟡 1 mod/);
  assert.match(summary, /🔴 1 rem/);
});

test('buildPayload em falha adiciona próximos passos', () => {
  const payload = buildPayload({
    version: '1.2.3',
    status: 'failure',
    serverUrl: 'https://github.com',
    repository: '085-Digital/porno-mineiro-wp',
    sha: 'abc1234567890',
    runId: '123',
    actor: 'rhuan',
    branch: 'develop',
    gitInfo: baseGitInfo,
  });

  const fieldNames = payload.embeds[0].fields.map((field) => field.name);

  assert.ok(fieldNames.some((name) => name.includes('Próximos passos')));
  assert.match(payload.embeds[0].title, /❌/);
});

test('categorizeCommit retorna categoria com emoji', () => {
  assert.equal(categorizeCommit('fix: corrige bug').label, 'Correção');
  assert.equal(categorizeCommit('fix: corrige bug').emoji, '🐛');
  assert.equal(categorizeCommit('algo aleatório').label, 'Outros');
});
