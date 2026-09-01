#!/usr/bin/env node

'use strict';

const { execSync } = require('child_process');
const fs = require('fs');

// Ajuste para o nome do projeto ao aplicar esta skill.
const PROJECT_NAME = 'Porno Español';

function run(command, fallback = '') {
  try {
    return execSync(command, { encoding: 'utf8' }).trim();
  } catch {
    return fallback;
  }
}

function truncate(text, max) {
  if (!text) return '';
  if (text.length <= max) return text;
  return `${text.substring(0, max - 15)}\n...(truncado)`;
}

function getGitInfo() {
  const commitHash = run('git log -1 --pretty=format:%h');
  const commitFull = run('git log -1 --pretty=format:%H');
  const commitMsg = run('git log -1 --pretty=format:%s', 'Sem mensagem de commit');
  const commitAuthor = run('git log -1 --pretty=format:%an');
  const commitDate = run('git log -1 --pretty=format:%cI');
  const diffRaw = run('git diff --name-status HEAD~1 HEAD');
  const fileChanges = [];

  if (diffRaw) {
    for (const line of diffRaw.split('\n')) {
      if (!line.trim()) continue;

      const parts = line.split('\t');
      const status = parts[0];

      if (status.startsWith('R') || status.startsWith('C')) {
        fileChanges.push({ status, oldFile: parts[1], file: parts[2] });
      } else {
        fileChanges.push({ status, file: parts[1] });
      }
    }
  }

  return { commitHash, commitFull, commitMsg, commitAuthor, commitDate, fileChanges };
}

const CATEGORIES = [
  { emoji: '✨', label: 'Feature', test: (msg) => /^feat|^add|\badd\b|\bnew\b/.test(msg) },
  { emoji: '🐛', label: 'Correção', test: (msg) => /^fix|\bbug\b|\bcorrig/.test(msg) },
  { emoji: '♻️', label: 'Refatoração', test: (msg) => /^refactor|\bclean\b|\brefator/.test(msg) },
  { emoji: '⚡', label: 'Performance', test: (msg) => /^perf|\bperformance\b/.test(msg) },
  { emoji: '🎨', label: 'Estilo', test: (msg) => /^style|\bui\b|\bdesign\b/.test(msg) },
  { emoji: '📝', label: 'Documentação', test: (msg) => /^docs|\breadme\b/.test(msg) },
  { emoji: '🔒', label: 'Segurança', test: (msg) => /^security|\bsecur/.test(msg) },
  { emoji: '🔧', label: 'Manutenção', test: (msg) => /^chore|\bvers[aã]o\b|\bversion\b|\brelease\b/.test(msg) },
];

const DEFAULT_CATEGORY = { emoji: '📦', label: 'Outros' };

function categorizeCommit(message) {
  const normalized = (message || '').toLowerCase();
  const match = CATEGORIES.find((category) => category.test(normalized));

  return match || DEFAULT_CATEGORY;
}

const FILE_GROUPS = [
  { key: 'A', emoji: '🟢', label: 'Adicionados' },
  { key: 'M', emoji: '🟡', label: 'Modificados' },
  { key: 'D', emoji: '🔴', label: 'Removidos' },
  { key: 'R', emoji: '🔵', label: 'Renomeados' },
  { key: 'C', emoji: '🟣', label: 'Copiados' },
];

function groupFiles(fileChanges) {
  const grouped = {};
  const other = [];

  for (const group of FILE_GROUPS) grouped[group.key] = [];

  for (const file of fileChanges) {
    const group = FILE_GROUPS.find((candidate) => file.status.startsWith(candidate.key));

    if (group) {
      grouped[group.key].push(file);
    } else {
      other.push(file);
    }
  }

  const result = FILE_GROUPS
    .filter((group) => grouped[group.key].length > 0)
    .map((group) => ({ ...group, files: grouped[group.key] }));

  if (other.length) result.push({ key: 'X', emoji: '⚪', label: 'Outros', files: other });

  return result;
}

function buildFilesText(fileChanges) {
  const maxPerGroup = 12;
  const groups = groupFiles(fileChanges);
  const parts = [];

  for (const group of groups) {
    const shown = group.files.slice(0, maxPerGroup);
    const extra = group.files.length - maxPerGroup;

    let text = `${group.emoji} **${group.label} (${group.files.length})**\n`;
    text += shown
      .map((file) => (
        file.oldFile
          ? `\`${file.oldFile}\` → \`${file.file}\``
          : `\`${file.file}\``
      ))
      .join('\n');

    if (extra > 0) text += `\n… e mais ${extra} arquivo(s)`;
    parts.push(text);
  }

  return truncate(parts.join('\n\n'), 1024) || '*Nenhum arquivo detectado*';
}

function buildSummary({ version, branch, actor, commitHash, sha, serverUrl, repository, fileChanges }) {
  const countByStatus = (key) => fileChanges.filter((file) => file.status.startsWith(key)).length;
  const added = countByStatus('A');
  const modified = countByStatus('M');
  const deleted = countByStatus('D');
  const moved = countByStatus('R') + countByStatus('C');

  const stats = [];
  if (added) stats.push(`🟢 ${added} add`);
  if (modified) stats.push(`🟡 ${modified} mod`);
  if (deleted) stats.push(`🔴 ${deleted} rem`);
  if (moved) stats.push(`🔵 ${moved} mov`);

  const statsLine = stats.length ? stats.join(' · ') : 'sem alterações detectadas';

  return [
    `🏷️ **Versão:** \`${version}\``,
    `🌿 **Branch:** \`${branch || 'desconhecida'}\``,
    `👤 **Autor:** ${actor || 'desconhecido'}`,
    `🔗 **Commit:** [\`${commitHash}\`](${serverUrl}/${repository}/commit/${sha})`,
    `📊 **Arquivos (${fileChanges.length}):** ${statsLine}`,
  ].join('\n');
}

function buildPayload({
  version,
  status,
  serverUrl,
  repository,
  sha,
  runId,
  actor,
  branch,
  gitInfo = getGitInfo(),
}) {
  const linkSha = sha || gitInfo.commitFull || gitInfo.commitHash;
  const linkHash = gitInfo.commitHash || linkSha.substring(0, 7);
  const commitMsg = gitInfo.commitMsg;
  const category = categorizeCommit(commitMsg);
  const isSuccess = status === 'success';
  const isManual = status === 'manual';
  const color = isSuccess ? 0x22c55e : isManual ? 0x3b82f6 : 0xef4444;
  const branchName = branch || 'desconhecida';

  const statusEmoji = isSuccess ? '✅' : isManual ? '📢' : '❌';
  const contentEmoji = isSuccess ? '🚀' : isManual ? '📢' : '🔥';

  const title = isSuccess
    ? `${statusEmoji} ${PROJECT_NAME} — Deploy concluído v${version}`
    : isManual
      ? `${statusEmoji} ${PROJECT_NAME} — Notificação manual v${version}`
      : `${statusEmoji} ${PROJECT_NAME} — Deploy falhou v${version}`;

  const description = isSuccess || isManual
    ? `${category.emoji} **${category.label}:** ${commitMsg}`
    : `Deploy falhou na branch \`${branchName}\`. Verifique os logs para mais detalhes.`;

  const fields = [
    {
      name: '📋 Resumo',
      value: truncate(buildSummary({
        version,
        branch: branchName,
        actor: actor || gitInfo.commitAuthor,
        commitHash: linkHash,
        sha: linkSha,
        serverUrl,
        repository,
        fileChanges: gitInfo.fileChanges,
      }), 1024),
      inline: false,
    },
  ];

  if (gitInfo.fileChanges.length > 0) {
    fields.push({
      name: `📂 Arquivos alterados (${gitInfo.fileChanges.length})`,
      value: buildFilesText(gitInfo.fileChanges),
      inline: false,
    });
  }

  fields.push({
    name: '🔗 Links',
    value: [
      `[📝 Ver commit](${serverUrl}/${repository}/commit/${linkSha})`,
      `[⚙️ Logs da action](${serverUrl}/${repository}/actions/runs/${runId})`,
      `[📁 Repositório](${serverUrl}/${repository})`,
    ].join(' • '),
    inline: false,
  });

  if (!isSuccess && !isManual) {
    fields.push({
      name: '🛠️ Próximos passos',
      value: '1️⃣ Verificar os logs de erro\n2️⃣ Corrigir o problema identificado\n3️⃣ Fazer novo push para tentar novamente',
      inline: false,
    });
  }

  return {
    content: `${contentEmoji} ${PROJECT_NAME} • Branch: \`${branchName}\` • Commit: \`${linkHash}\``,
    embeds: [{
      title,
      description,
      color,
      fields,
      timestamp: gitInfo.commitDate || new Date().toISOString(),
      footer: { text: `${PROJECT_NAME} • GitHub Actions` },
    }],
  };
}

function validatePayload(payload) {
  if (payload.content.length > 2000) {
    throw new Error('O conteúdo da mensagem excede 2000 caracteres');
  }

  const embed = payload.embeds[0];

  if (embed.title.length > 256) {
    throw new Error('O título do embed excede 256 caracteres');
  }

  for (const field of embed.fields) {
    if (field.name.length > 256) {
      throw new Error(`O nome do campo "${field.name.substring(0, 40)}" excede 256 caracteres`);
    }

    if (field.value.length > 1024) {
      throw new Error(`O valor do campo "${field.name}" excede 1024 caracteres`);
    }
  }
}

function main() {
  const [
    ,
    ,
    version = '1.0.0',
    status = 'success',
    serverUrl = '',
    repository = '',
    sha = '',
    runId = '',
    actor = '',
    branch = '',
  ] = process.argv;

  const payload = buildPayload({ version, status, serverUrl, repository, sha, runId, actor, branch });
  validatePayload(payload);

  const outputPath = process.env.DISCORD_PAYLOAD_FILE || 'discord_payload.json';
  fs.writeFileSync(outputPath, JSON.stringify(payload, null, 2));

  console.log(`Payload Discord gerado: ${outputPath}`);
  console.log(`Projeto: ${PROJECT_NAME} | Versão: ${version} | Branch: ${branch || 'desconhecida'} | Status: ${status}`);
}

if (require.main === module) {
  main();
}

module.exports = {
  buildPayload,
  buildSummary,
  categorizeCommit,
};
