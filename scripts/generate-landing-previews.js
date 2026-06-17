#!/usr/bin/env node

/**
 * Génère les captures d'écran pour la page vitrine Qualyra
 * Usage: node scripts/generate-landing-previews.js
 *
 * Prérequis :
 *   1. Docker container qualyra actif (port 8000)
 *   2. Démo seedée : docker exec qualyra php artisan db:seed --class=DemoSeeder
 *   3. Puppeteer installé (npm install)
 * Sortie: public/qualyra/img/preview-*.png
 */

import puppeteer from 'puppeteer';
import { mkdirSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '..');
const IMG_DIR = join(ROOT, 'public', 'qualyra', 'img');
const APP_URL = process.env.APP_URL || 'http://localhost:8000';

async function main() {
  console.log(`\n📸 Génération des previews pour la page vitrine...\n`);

  mkdirSync(IMG_DIR, { recursive: true });

  const browser = await puppeteer.launch({
    headless: true,
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-dev-shm-usage',
      '--disable-gpu',
    ],
  });

  try {
    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900 });

    console.log('🔑 Connexion...');
    await page.goto(`${APP_URL}/login`, { waitUntil: 'networkidle0' });
    await page.type('input[name="email"]', 'demo@example.com', { delay: 30 });
    await page.type('input[name="password"]', 'password', { delay: 15 });
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle0' }),
      page.click('button[type="submit"]'),
    ]);
    console.log('✅ Connecté\n');

    // ── Dashboard dark ──
    console.log('📷 Dashboard — dark...');
    await page.evaluate(() => {
      document.documentElement.setAttribute('data-theme', 'dark');
      localStorage.setItem('qualyra-theme', 'dark');
    });
    await page.goto(`${APP_URL}/dashboard`, { waitUntil: 'networkidle0' });
    await page.waitForSelector('.dashboard-head', { timeout: 10000 });
    await sleep(1500);

    // Chartes qui commencent après ~400px. On capture jusqu'à 840px pour inclure
    // le donut de répartition des risques + le diagramme "Usages par domaine".
    await page.screenshot({
      path: join(IMG_DIR, 'preview-dashboard-dark.png'),
      clip: { x: 0, y: 0, width: 1440, height: 840 },
    });
    console.log('   ✅ preview-dashboard-dark.png');

    // ── Dashboard light ──
    console.log('📷 Dashboard — light...');
    await page.evaluate(() => {
      document.documentElement.setAttribute('data-theme', 'light');
      localStorage.setItem('qualyra-theme', 'light');
    });
    await sleep(500);
    await page.reload({ waitUntil: 'networkidle0' });
    await page.waitForSelector('.dashboard-head', { timeout: 10000 });
    await sleep(1500);

    await page.screenshot({
      path: join(IMG_DIR, 'preview-dashboard-light.png'),
      clip: { x: 0, y: 0, width: 1440, height: 840 },
    });
    console.log('   ✅ preview-dashboard-light.png\n');

    // ── Rapport show — pages ──
    console.log('📷 Rapport — pages...');
    const lastReportId = await getLastReportId(page);
    if (!lastReportId) {
      console.error('❌ Aucun rapport trouvé. Seeder d\'abord : docker exec qualyra php artisan db:seed --class=DemoSeeder');
      process.exit(1);
    }
    console.log(`   ID rapport: ${lastReportId}`);

    const pageUrl = `${APP_URL}/reports/${lastReportId}`;
    await page.goto(pageUrl, { waitUntil: 'networkidle0' });
    await page.waitForSelector('.report-paper__sheet', { timeout: 10000 });
    await sleep(1500);

    // Pleine page pour connaître la hauteur réelle
    const bodyHeight = await page.evaluate(() => document.body.scrollHeight);

    // Capturer 3 sections chevauchantes pour l'éventail
    // Section 1 — couverture + niveau de risque (haut)
    const secH = Math.min(bodyHeight, 650);
    await page.screenshot({
      path: join(IMG_DIR, 'preview-report-1.png'),
      clip: { x: 0, y: 0, width: 1440, height: secH },
    });
    console.log('   ✅ preview-report-1.png');

    // Section 2 — milieu (synthèse exécutive)
    if (bodyHeight > 300) {
      const midY = Math.min(Math.floor(bodyHeight * 0.25), bodyHeight - secH);
      await page.screenshot({
        path: join(IMG_DIR, 'preview-report-2.png'),
        clip: { x: 0, y: midY, width: 1440, height: secH },
      });
      console.log('   ✅ preview-report-2.png');
    } else {
      // Fallback: copier report-1
      await page.screenshot({
        path: join(IMG_DIR, 'preview-report-2.png'),
        clip: { x: 0, y: 0, width: 1440, height: secH },
      });
      console.log('   ✅ preview-report-2.png (fallback)');
    }

    // Section 3 — bas (plan d'action)
    if (bodyHeight > 600) {
      const lowY = Math.min(Math.floor(bodyHeight * 0.5), bodyHeight - secH);
      await page.screenshot({
        path: join(IMG_DIR, 'preview-report-3.png'),
        clip: { x: 0, y: lowY, width: 1440, height: secH },
      });
      console.log('   ✅ preview-report-3.png');
    } else {
      await page.screenshot({
        path: join(IMG_DIR, 'preview-report-3.png'),
        clip: { x: 0, y: 0, width: 1440, height: secH },
      });
      console.log('   ✅ preview-report-3.png (fallback)');
    }

    console.log('\n✅ Toutes les previews générées');
  } finally {
    await browser.close();
  }
}

async function getLastReportId(page) {
  await page.goto(`${APP_URL}/reports`, { waitUntil: 'networkidle0' });
  try {
    await page.waitForSelector('.surface a[href*="/reports/"]', { timeout: 5000 });
    const href = await page.$eval('.surface a[href*="/reports/"]', (el) => el.getAttribute('href'));
    const match = href.match(/\/reports\/(\d+)/);
    return match ? match[1] : null;
  } catch {
    return null;
  }
}

main().catch((err) => {
  console.error('❌ Erreur:', err);
  process.exit(1);
});
