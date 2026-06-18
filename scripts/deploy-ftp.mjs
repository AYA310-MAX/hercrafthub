/**
 * Upload HerCraft Hub to InfinityFree via FTP.
 * Usage: node scripts/deploy-ftp.mjs
 * Requires: config/deploy.local.php
 */
import ftp from 'basic-ftp';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { pathToFileURL } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, '..');
const DEPLOY_CONFIG = path.join(ROOT, 'config', 'deploy.local.php');

const SKIP_DIRS = new Set([
  'node_modules', '.git', 'docs', 'scripts', '.cursor',
]);
const SKIP_FILES = new Set([
  'db.local.php', 'deploy.local.php', 'deploy.local.example.php',
  'package.json', 'package-lock.json', '.gitignore', '.installed',
  'HOST.md', 'install.php',
]);
const SKIP_PREFIXES = ['.'];

function loadDeployConfig() {
  if (!fs.existsSync(DEPLOY_CONFIG)) {
    console.error('Missing config/deploy.local.php');
    console.error('Copy config/deploy.local.example.php and set your FTP password.');
    process.exit(1);
  }
  const text = fs.readFileSync(DEPLOY_CONFIG, 'utf8');
  const get = (name) => {
    const m = text.match(new RegExp(`define\\('${name}',\\s*'([^']*)'\\)`));
    return m ? m[1] : '';
  };
  return {
    host: get('FTP_HOST'),
    user: get('FTP_USER'),
    password: get('FTP_PASS'),
    remoteDir: get('FTP_REMOTE_DIR') || '/htdocs',
  };
}

function walk(dir, base = dir) {
  const entries = [];
  for (const name of fs.readdirSync(dir)) {
    if (SKIP_PREFIXES.some((p) => name.startsWith(p) && name !== '.gitkeep')) continue;
    const full = path.join(dir, name);
    const rel = path.relative(base, full).replace(/\\/g, '/');
    const stat = fs.statSync(full);
    if (stat.isDirectory()) {
      if (SKIP_DIRS.has(name)) continue;
      entries.push(...walk(full, base));
    } else if (!SKIP_FILES.has(name)) {
      entries.push({ local: full, remote: rel });
    }
  }
  return entries;
}

async function main() {
  const cfg = loadDeployConfig();
  if (!cfg.password || cfg.password.includes('YOUR_')) {
    console.error('Set a real FTP password in config/deploy.local.php');
    process.exit(1);
  }

  const client = new ftp.Client(120000);
  client.ftp.verbose = false;

  console.log(`Connecting to ${cfg.host} as ${cfg.user}...`);
  await client.access({
    host: cfg.host,
    user: cfg.user,
    password: cfg.password,
    secure: false,
  });

  await client.ensureDir(cfg.remoteDir);
  await client.cd(cfg.remoteDir);

  const files = walk(ROOT);
  console.log(`Uploading ${files.length} files to ${cfg.remoteDir}...`);

  for (const { local, remote } of files) {
    const remoteDir = path.posix.dirname(remote);
    if (remoteDir && remoteDir !== '.') {
      await client.ensureDir(remoteDir);
    }
    await client.uploadFrom(local, remote);
    console.log('  ↑', remote);
  }

  // Upload install.php separately (one-time setup)
  await client.uploadFrom(path.join(ROOT, 'install.php'), 'install.php');
  console.log('  ↑ install.php');

  // Remove default InfinityFree placeholder if present
  try {
    await client.remove('index2.html');
    console.log('  ✕ removed index2.html');
  } catch {
    /* may not exist */
  }

  client.close();
  console.log('\nDeploy complete!');
  console.log('1. Visit https://hercrafthub.infinityfreeapp.com/install.php');
  console.log('2. Delete install.php from the server after setup');
  console.log('3. Site: https://hercrafthub.infinityfreeapp.com/');
}

main().catch((err) => {
  console.error('Deploy failed:', err.message);
  process.exit(1);
});
