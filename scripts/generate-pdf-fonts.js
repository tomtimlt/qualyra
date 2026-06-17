#!/usr/bin/env node

/**
 * Génère le CSS base64 pour les polices du PDF
 * Usage: node scripts/generate-pdf-fonts.js
 * Sortie: resources/views/reports/pdf-fonts.blade.php
 */

import { readFileSync, writeFileSync, existsSync } from 'fs';
import { join } from 'path';

const FONTS_DIR = join(process.cwd(), 'public', 'fonts');
const OUTPUT = join(process.cwd(), 'resources', 'views', 'reports', 'pdf-fonts.blade.php');

const FONT_FILES = [
  { family: 'Geist', weight: 300, style: 'normal', file: 'Geist-300.ttf' },
  { family: 'Geist', weight: 400, style: 'normal', file: 'Geist-400.ttf' },
  { family: 'Geist', weight: 500, style: 'normal', file: 'Geist-500.ttf' },
  { family: 'Geist', weight: 600, style: 'normal', file: 'Geist-600.ttf' },
  { family: 'Geist', weight: 700, style: 'normal', file: 'Geist-700.ttf' },
  { family: 'Geist Mono', weight: 400, style: 'normal', file: 'GeistMono-400.ttf' },
  { family: 'Geist Mono', weight: 500, style: 'normal', file: 'GeistMono-500.ttf' },
  { family: 'Instrument Serif', weight: 400, style: 'normal', file: 'InstrumentSerif-400.ttf' },
  { family: 'Instrument Serif', weight: 400, style: 'italic', file: 'InstrumentSerif-400Italic.ttf' },
];

function toBase64(filePath) {
  const buffer = readFileSync(filePath);
  return buffer.toString('base64');
}

async function main() {
  console.log('🔤 Génération des fonts base64 pour le PDF...');

  let cssContent = '<style>\n/* Polices embeddées en base64 pour PDF (Chrome headless) */\n\n';

  for (const { family, weight, style, file } of FONT_FILES) {
    const filePath = join(FONTS_DIR, file);
    if (!existsSync(filePath)) {
      console.error(`  ❌ Fichier manquant: ${file}`);
      continue;
    }

    const base64 = toBase64(filePath);
    const format = file.endsWith('.ttf') ? 'truetype' : 'woff2';

    cssContent += `@font-face {\n`;
    cssContent += `  font-family: '${family}';\n`;
    cssContent += `  font-weight: ${weight};\n`;
    cssContent += `  font-style: ${style};\n`;
    cssContent += `  src: url(data:font/${format};base64,${base64}) format('${format}');\n`;
    cssContent += `}\n\n`;

    console.log(`  ✅ ${file} (${Math.round(base64.length / 1024)} KB base64)`);
  }

  cssContent += '</style>';

  writeFileSync(OUTPUT, cssContent);
  console.log(`\n📝 Généré: ${OUTPUT}`);
  console.log('✅ Terminé');
}

main().catch(err => {
  console.error('❌ Erreur:', err);
  process.exit(1);
});