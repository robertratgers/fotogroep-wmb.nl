#!/usr/bin/env node
/**
 * Convert all .jpg and .jpeg under `assets` (or provided --dir) to .webp and remove originals after successful conversion.
 * Also slugifies filenames found in any path segment named `activiteiten` or `leden`.
 *
 * Usage:
 *   1. npm install sharp
 *   2. node scripts/convert-images.js --dry-run --dir="D:\\...\\assets"
 *   3. node scripts/convert-images.js --dir="D:\\...\\assets" --quality=85 --backup
 *
 * Options:
 *   --dry-run             : Show actions without writing/removing files
 *   --quality=<1-100>     : WebP quality (default 80)
 *   --dir=<path>          : Assets directory to scan (default: ./assets)
 *   --log=<path>          : Path to append a log of actions (default: ./scripts/convert-images.log)
 *   --backup              : Create a recursive backup copy before converting (default: off)
 *   --backup-dir=<path>   : Optional directory to place the backup. If omitted, a timestamped folder next to assets is used.
 */

const fsPromises = require('fs').promises;
const fs = require('fs');
const path = require('path');
let sharp;
try {
  sharp = require('sharp');
} catch (e) {
  console.error('Missing dependency: sharp. Run `npm install sharp` and retry.');
  process.exit(1);
}

const argv = process.argv.slice(2);
const dryRun = argv.includes('--dry-run');
const qArg = argv.find(a => a.startsWith('--quality='));
const dirArg = argv.find(a => a.startsWith('--dir='));
const logArg = argv.find(a => a.startsWith('--log='));
const backupFlag = argv.includes('--backup');
const backupDirArg = argv.find(a => a.startsWith('--backup-dir='));

const quality = qArg ? Math.max(1, Math.min(100, parseInt(qArg.split('=')[1], 10) || 80)) : 80;
const ASSETS_DIR = dirArg ? path.resolve(dirArg.split('=')[1]) : path.resolve(process.cwd(), 'assets');
const LOG_FILE = logArg ? path.resolve(logArg.split('=')[1]) : path.resolve(process.cwd(), 'scripts', 'convert-images.log');
const BACKUP_DIR_OVERRIDE = backupDirArg ? path.resolve(backupDirArg.split('=')[1]) : null;
const RENAME_DIRS = ['activiteiten', 'leden']; // directories to apply slugified logical names

async function appendLog(line) {
  try {
    const dir = path.dirname(LOG_FILE);
    await fsPromises.mkdir(dir, { recursive: true });
    const ts = new Date().toISOString();
    await fsPromises.appendFile(LOG_FILE, `[${ts}] ${line}\n`, 'utf8');
  } catch (e) {
    // don't fail on logging
  }
}

function slugify(name) {
  return name
    .normalize('NFKD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim()
    .replace(/[\s_]+/g, '-')
    .replace(/[^a-z0-9-\.]/g, '')
    .replace(/-+/g, '-')
    .replace(/^-+|-+$/g, '');
}

async function walk(dir) {
  const entries = await fsPromises.readdir(dir, { withFileTypes: true });
  const results = [];
  for (const e of entries) {
    const full = path.join(dir, e.name);
    if (e.isDirectory()) {
      results.push(...await walk(full));
    } else {
      results.push(full);
    }
  }
  return results;
}

function inRenameDir(filePath) {
  const parts = filePath.split(path.sep).map(p => p.toLowerCase());
  return parts.includes('assets') && parts.some(p => RENAME_DIRS.includes(p));
}

async function ensureUnique(dest) {
  const ext = path.extname(dest);
  let name = path.basename(dest, ext);
  const dir = path.dirname(dest);
  let candidate = path.join(dir, `${name}${ext}`);
  let counter = 1;
  while (true) {
    try {
      await fsPromises.access(candidate);
      candidate = path.join(dir, `${name}-${counter}${ext}`);
      counter++;
    } catch (ex) {
      return candidate;
    }
  }
}

async function copyDirRecursive(src, dest) {
  // prefer fs.cp if available (Node 16.7+), otherwise fallback
  try {
    if (fsPromises.cp) {
      await fsPromises.cp(src, dest, { recursive: true });
      return;
    }
  } catch (e) {
    // fall through to manual copy
  }

  // Manual copy fallback
  await fsPromises.mkdir(dest, { recursive: true });
  const entries = await fsPromises.readdir(src, { withFileTypes: true });
  for (const entry of entries) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);
    if (entry.isDirectory()) {
      await copyDirRecursive(srcPath, destPath);
    } else if (entry.isFile()) {
      await fsPromises.copyFile(srcPath, destPath);
    }
  }
}

(async () => {
  try {
    await fsPromises.access(ASSETS_DIR);
  } catch (err) {
    const msg = `Cannot find assets directory at ${ASSETS_DIR}. Run this from project root or adjust --dir.`;
    console.error(msg);
    await appendLog(`ERROR: ${msg}`);
    process.exit(1);
  }

  // Optional backup
  if (backupFlag) {
    let backupTarget = BACKUP_DIR_OVERRIDE;
    if (!backupTarget) {
      const parent = path.dirname(ASSETS_DIR);
      const base = path.basename(ASSETS_DIR);
      const ts = new Date().toISOString().replace(/[:.]/g, '-');
      backupTarget = path.join(parent, `${base}-backup-${ts}`);
    }

    const relBackup = path.relative(process.cwd(), backupTarget);
    console.log(`Creating backup ${relBackup} ...`);
    await appendLog(`BACKUP: from ${ASSETS_DIR} -> ${backupTarget}`);
    if (!dryRun) {
      try {
        await copyDirRecursive(ASSETS_DIR, backupTarget);
        await appendLog(`BACKUP_OK: ${backupTarget}`);
        console.log('Backup created.');
      } catch (e) {
        const msg = `Failed to create backup: ${e.message}`;
        console.error(msg);
        await appendLog(`BACKUP_FAILED: ${msg}`);
        process.exit(1);
      }
    }
  }

  const allFiles = await walk(ASSETS_DIR);
  const images = allFiles.filter(f => /\.(jpe?g)$/i.test(f));

  if (images.length === 0) {
    const msg = 'No .jpg or .jpeg files found under assets. Nothing to do.';
    console.log(msg);
    await appendLog(msg);
    return;
  }

  console.log(`Found ${images.length} jpeg/jpg files. Quality=${quality}. Dry-run=${dryRun}\n`);
  await appendLog(`Start: found ${images.length} jpeg/jpg files. Quality=${quality}. Dry-run=${dryRun}`);

  for (const img of images) {
    const dir = path.dirname(img);
    const ext = path.extname(img);
    const base = path.basename(img, ext);
    let dest;

    if (inRenameDir(img)) {
      const slug = slugify(base) || 'image';
      dest = path.join(dir, `${slug}.webp`);
      dest = await ensureUnique(dest);
    } else {
      dest = path.join(dir, `${base}.webp`);
      dest = await ensureUnique(dest);
    }

    const relSrc = path.relative(process.cwd(), img);
    const relDest = path.relative(process.cwd(), dest);
    const actionMsg = `${dryRun ? '[DRY] ' : ''}Convert: ${relSrc} -> ${relDest}`;
    console.log(actionMsg);
    await appendLog(actionMsg);

    if (!dryRun) {
      try {
        await sharp(img).webp({ quality }).toFile(dest);
        await fsPromises.access(dest);
        await fsPromises.unlink(img);
        const okMsg = `OK: converted and removed original -> ${relDest}`;
        console.log(okMsg);
        await appendLog(okMsg);
      } catch (e) {
        const errMsg = `FAILED: ${relSrc} -> ${e.message}`;
        console.error(errMsg);
        await appendLog(errMsg);
      }
    }
  }

  const doneMsg = 'Done.';
  console.log('\n' + doneMsg);
  await appendLog(doneMsg);
})();
