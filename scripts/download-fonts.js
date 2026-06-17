#!/usr/bin/env node

/**
 * Télécharge les polices Google Fonts localement pour Qualyra
 * Usage: node scripts/download-fonts.js
 * Sortie: public/fonts/*.ttf + public/fonts/fonts.css
 */

import { mkdirSync, existsSync, writeFileSync, promises as fs } from 'fs';
import { join } from 'path';
import https from 'https';

const FONTS_DIR = join(process.cwd(), 'public', 'fonts');
const CSS_OUTPUT = join(FONTS_DIR, 'fonts.css');

const FONT_SPECS = [
  { family: 'Geist', weights: [300, 400, 500, 600, 700], styles: ['normal'] },
  { family: 'Geist Mono', weights: [400, 500], styles: ['normal'] },
  { family: 'Instrument Serif', weights: [400], styles: ['normal', 'italic'] },
];

function fetchCss(url) {
  return new Promise((resolve, reject) => {
    https.get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, res => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => resolve(data));
    }).on('error', reject);
  });
}

function downloadFile(url, destPath) {
  return new Promise((resolve, reject) => {
    https.get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, res => {
      if (res.statusCode !== 200) {
        return reject(new Error(`HTTP ${res.statusCode} for ${url}`));
      }
      const chunks = [];
      res.on('data', chunk => chunks.push(chunk));
      res.on('end', async () => {
        await fs.writeFile(destPath, Buffer.concat(chunks));
        resolve();
      });
    }).on('error', reject);
  });
}

async function main() {
  if (!existsSync(FONTS_DIR)) {
    mkdirSync(FONTS_DIR, { recursive: true });
  }

  console.log('📥 Téléchargement des polices Google Fonts (TTF)...');

  let cssContent = '/* Qualyra — Polices locales (généré automatiquement) */\n\n';

  for (const { family, weights, styles } of FONT_SPECS) {
    const familyParam = family.replace(/\s+/g, '+');
    const weightStyles = weights.flatMap(w => styles.map(s => `${w}${s === 'italic' ? 'i' : ''}`)).join(',');
    const cssUrl = `https://fonts.googleapis.com/css?family=${familyParam}:${weightStyles}&display=swap`;

    try {
      const css = await fetchCss(cssUrl);

      const fontFaceRegex = /@font-face\s*{([^}]+)}/g;
      const urlRegex = /url\(([^)]+)\)\s+format\('truetype'\)/;
      const propRegex = /(\w+(?:-\w+)?)\s*:\s*([^;]+);/g;

      let match;
      while ((match = fontFaceRegex.exec(css)) !== null) {
        const block = match[1];
        const urlMatch = urlRegex.exec(block);
        if (!urlMatch) continue;

        const fontUrl = urlMatch[1];
        const props = {};
        let propMatch;
        while ((propMatch = propRegex.exec(block)) !== null) {
          props[propMatch[1].trim()] = propMatch[2].trim();
        }

        const weight = props['font-weight'] || '400';
        const style = props['font-style'] || 'normal';
        const display = props['font-display'] || 'swap';

        const ext = fontUrl.split('.').pop().split('?')[0];
        const fileName = `${family.replace(/\s+/g, '')}-${weight}${style === 'italic' ? 'Italic' : ''}.${ext}`;
        const destPath = join(FONTS_DIR, fileName);

        await downloadFile(fontUrl, destPath);
        console.log(`  ✅ ${fileName}`);

        cssContent += `@font-face {\n`;
        cssContent += `  font-family: '${family}';\n`;
        cssContent += `  font-weight: ${weight};\n`;
        cssContent += `  font-style: ${style};\n`;
        cssContent += `  font-display: ${display};\n`;
        cssContent += `  src: url('/fonts/${fileName}') format('truetype');\n`;
        cssContent += `}\n\n`;
      }
    } catch (err) {
      console.error(`  ❌ Erreur ${family}:`, err.message);
    }
  }

  writeFileSync(CSS_OUTPUT, cssContent);
  console.log(`\n📝 CSS généré: ${CSS_OUTPUT}`);
  console.log('✅ Terminé');
}

main().catch(err => {
  console.error('❌ Erreur fatale:', err);
  process.exit(1);
});