/**
 * Captures responsive screenshots for HerCraft Hub deliverable.
 * Run: node scripts/capture-screenshots.mjs
 * Requires: npm install puppeteer (from project root)
 */
import puppeteer from 'puppeteer';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, '..');
const BASE = 'http://localhost/hercrafthub';
const OUT = path.join(ROOT, 'docs', 'screenshots');

const VIEWPORTS = {
  desktop: { width: 1280, height: 900 },
  tablet: { width: 768, height: 1024 },
  mobile: { width: 375, height: 812 },
};


const MAIN_PAGES = [
  { id: 'home', path: '/index.php' },
  { id: 'browse', path: '/browse.php' },
  { id: 'listing', path: '/listing.php?id=1' },
  { id: 'login', path: '/login.php' },
  { id: 'register', path: '/register.php' },
  { id: 'sell', path: '/sell.php', role: 'seller' },
];

const ADMIN_PAGES = [
  { id: 'dashboard', path: '/admin/index.php' },
  { id: 'users', path: '/admin/users.php' },
  { id: 'create-user', path: '/admin/create_user.php' },
  { id: 'edit-user', path: '/admin/edit_user.php?id=2' },
  { id: 'listings', path: '/admin/listings.php' },
];

const FINAL_SHOTS = [
  { file: 'final-home.png', path: '/index.php', viewport: 'desktop' },
  { file: 'final-browse.png', path: '/browse.php', viewport: 'desktop' },
  { file: 'final-login.png', path: '/login.php', viewport: 'desktop' },
  { file: 'final-sell.png', path: '/sell.php', role: 'seller', viewport: 'desktop' },
  { file: 'final-admin-dashboard.png', path: '/admin/index.php', role: 'admin', viewport: 'desktop' },
  { file: 'final-admin-users.png', path: '/admin/users.php', role: 'admin', viewport: 'desktop' },
  { file: 'final-admin-listings.png', path: '/admin/listings.php', role: 'admin', viewport: 'desktop' },
];

function ensureDirs() {
  for (const sub of ['main', 'admin', 'final', 'mysql']) {
    fs.mkdirSync(path.join(OUT, sub), { recursive: true });
  }
}

async function bootstrapSession(page, role) {
  await page.goto(`${BASE}/scripts/bootstrap_session.php?role=${role}`, {
    waitUntil: 'networkidle2',
    timeout: 30000,
  });
}

async function capture(page, url, outFile, viewport) {
  const vp = VIEWPORTS[viewport];
  await page.setViewport(vp);
  await page.goto(`${BASE}${url}`, { waitUntil: 'networkidle2', timeout: 30000 });
  await page.evaluate(() => window.scrollTo(0, 0));
  await new Promise((r) => setTimeout(r, 500));
  await page.screenshot({ path: outFile, fullPage: true });
  console.log('Saved:', path.relative(ROOT, outFile));
}

async function captureMysqlTables(page) {
  await page.setViewport(VIEWPORTS.desktop);
  const tables = ['users', 'categories', 'products', 'wishlists'];
  for (const table of tables) {
    const out = path.join(OUT, 'mysql', `table-${table}.png`);
    await page.goto(`${BASE}/scripts/table_view.php?table=${table}`, {
      waitUntil: 'networkidle2',
      timeout: 30000,
    });
    await page.screenshot({ path: out, fullPage: true });
    console.log('Saved:', path.relative(ROOT, out));
  }
}

async function main() {
  ensureDirs();
  const browser = await puppeteer.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });
  const page = await browser.newPage();

  for (const vp of Object.keys(VIEWPORTS)) {
    for (const p of MAIN_PAGES) {
      if (p.role) {
        await bootstrapSession(page, p.role);
      } else {
        const cookies = await page.cookies();
        await page.deleteCookie(...cookies);
      }
      const out = path.join(OUT, 'main', `${p.id}-${vp}.png`);
      await capture(page, p.path, out, vp);
    }
  }

  for (const vp of Object.keys(VIEWPORTS)) {
    await bootstrapSession(page, 'admin');
    for (const p of ADMIN_PAGES) {
      const out = path.join(OUT, 'admin', `${p.id}-${vp}.png`);
      await capture(page, p.path, out, vp);
    }
  }

  for (const shot of FINAL_SHOTS) {
    if (shot.role) await bootstrapSession(page, shot.role);
    const out = path.join(OUT, 'final', shot.file);
    await capture(page, shot.path, out, shot.viewport);
  }

  await captureMysqlTables(page);
  await browser.close();
  console.log('\nAll screenshots saved to docs/screenshots/');
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
