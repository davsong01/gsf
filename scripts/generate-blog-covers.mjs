// Editorial vector covers. Run locally with: node scripts/generate-blog-covers.mjs
// The generated PNGs are committed; production only needs the PHP seeder.
import { readFileSync, writeFileSync, mkdtempSync, rmSync, existsSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, resolve } from 'node:path';
import { execFileSync } from 'node:child_process';

const root = resolve(import.meta.dirname, '..');
const destination = join(root, 'resources/blog-covers');
const entries = JSON.parse(readFileSync(join(destination, 'manifest.json'), 'utf8'));
const font = [process.env.GSF_BLOG_COVER_FONT, '/System/Library/Fonts/Supplemental/Arial.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf'].find((path) => path && existsSync(path));
if (!font) throw new Error('Set GSF_BLOG_COVER_FONT to an installed TrueType font.');
const scratch = mkdtempSync(join(tmpdir(), 'gsf-blog-covers-'));
const palettes = [
  ['#0b2445', '#174f7a', '#f2c16a', '#b9dff4'],
  ['#123b48', '#197b7b', '#f4cb77', '#bdebe1'],
  ['#25204a', '#514287', '#eec782', '#ddd2fa'],
  ['#41273d', '#91516d', '#ffd08a', '#f7c4d9'],
  ['#1a3542', '#3b6680', '#e7b96d', '#cce3e9'],
  ['#283523', '#587a49', '#eacc87', '#d9ebc4']
];

function escapeXml(value) {
  return value.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&apos;');
}

function wrap(text, maxWidth) {
  const words = text.split(/\s+/);
  const lines = [];
  let line = '';
  for (const word of words) {
    if ((line + ' ' + word).trim().length > maxWidth && line) {
      lines.push(line);
      line = word;
    } else {
      line = (line + ' ' + word).trim();
    }
  }
  if (line) lines.push(line);
  return lines;
}

function drawMotif(motif) {
  const commands = ['push graphic-context', 'translate 50,162'];
  for (const match of motif.matchAll(/<(path|circle|rect)\b([^>]*)\/>/g)) {
    const attributes = Object.fromEntries([...match[2].matchAll(/([\w-]+)="([^"]*)"/g)].map((item) => [item[1], item[2]]));
    if (match[1] === 'path') commands.push(`path '${attributes.d}'`);
    if (match[1] === 'circle') commands.push(`circle ${attributes.cx},${attributes.cy} ${Number(attributes.cx) + Number(attributes.r)},${attributes.cy}`);
    if (match[1] === 'rect') commands.push(`roundrectangle ${attributes.x},${attributes.y} ${Number(attributes.x) + Number(attributes.width)},${Number(attributes.y) + Number(attributes.height)} ${attributes.rx ?? 0},${attributes.rx ?? 0}`);
  }
  commands.push('pop graphic-context');
  return commands.join(' ');
}

const paths = {
  prayer: '<path d="M145 248c-37-39-59-95-54-146 1-14 19-19 27-5l41 70-16-82c-3-18 21-25 27-8l27 83 20-70c5-18 29-13 27 5l-9 67 45-53c13-14 31-1 21 15l-73 124z"/><path d="M145 248c28 17 59 17 83 0"/>',
  spirit: '<path d="M193 46c21 54-30 62-5 110 4-34 34-43 44-76 62 67 45 145-7 180-53 36-127 11-128-56-1-44 30-69 53-93-8 31-6 53 8 68-7-49 6-92 35-133z"/><path d="M190 255c-19-22-17-40 3-61 19 24 26 43 4 64"/>',
  education: '<path d="M70 104c46-17 89-13 126 9v150c-43-22-83-27-126-11zM196 113c39-22 81-26 126-9v148c-43-16-83-11-126 11z"/><path d="M196 113v150M92 143c27-7 55-3 80 9M92 177c27-7 55-3 80 9M220 152c24-12 48-14 77-9M220 186c24-12 48-14 77-9"/>',
  power: '<path d="M205 35 112 182h71l-20 120 113-166h-74z"/><circle cx="194" cy="171" r="138" stroke-dasharray="4 22"/>',
  light: '<circle cx="196" cy="169" r="61"/><circle cx="196" cy="169" r="109" stroke-dasharray="3 20"/><path d="M196 21v41M196 276v41M47 169h42M303 169h42M89 62l30 29M272 247l30 29M302 62l-30 29M119 247l-30 29"/>',
  community: '<circle cx="196" cy="98" r="34"/><circle cx="100" cy="139" r="28"/><circle cx="292" cy="139" r="28"/><path d="M126 270v-20c0-42 30-70 70-70s70 28 70 70v20zM30 264v-15c0-35 29-59 70-59 14 0 24 2 31 8M362 264v-15c0-35-29-59-70-59-14 0-24 2-31 8"/>',
  leadership: '<path d="M72 272h252M100 270v-66h68v66M168 270v-108h67v108M235 270V90h65v180M72 169l92-71 58 37 93-86M282 49h34v34"/>',
  mountain: '<path d="m29 267 106-149 74 96 52-79 106 132zM94 177l42-59 41 54M243 162l18-27 20 29"/><circle cx="277" cy="67" r="28"/>',
  sevenfold: '<circle cx="196" cy="168" r="36"/><circle cx="196" cy="69" r="26"/><circle cx="282" cy="119" r="26"/><circle cx="282" cy="217" r="26"/><circle cx="196" cy="267" r="26"/><circle cx="110" cy="217" r="26"/><circle cx="110" cy="119" r="26"/><circle cx="330" cy="168" r="26"/>',
  wind: '<path d="M33 125h227c52 0 51-60 14-60-22 0-32 13-32 27M55 169h279c43 0 45 50 9 50-17 0-29-9-29-24M22 210h202c48 0 52 67 3 67-21 0-31-12-31-27"/>',
  stewardship: '<circle cx="195" cy="157" r="90"/><path d="M195 90v134M226 117c-22-18-62-13-62 12 0 35 62 14 62 49 0 26-37 32-68 14M107 282c47 20 129 20 177 0"/>',
  healing: '<path d="M165 53h62v75h75v62h-75v76h-62v-76H90v-62h75z"/><circle cx="196" cy="159" r="142" stroke-dasharray="4 20"/>',
  mercy: '<path d="M196 278 72 164c-48-49-2-121 57-98 30 11 49 34 67 57 18-23 37-46 67-57 59-23 105 49 57 98z"/><path d="M196 63v-32M94 68 72 47M298 68l22-21"/>',
  growth: '<path d="M195 290V143M195 190c-59 4-97-35-100-88 59-4 99 29 100 88zM197 159c6-54 42-82 95-81-4 53-44 82-95 81zM110 292h172"/>',
  church: '<path d="M64 278V126l132-76 132 76v152zM64 146l132-76 132 76M170 278v-80h52v80M102 194h35v38h-35M255 194h35v38h-35M196 52V17M179 34h34"/>',
  global: '<circle cx="196" cy="166" r="126"/><path d="M70 166h252M196 40v252M88 102h216M88 230h216M196 40c-63 46-63 206 0 252M196 40c63 46 63 206 0 252"/>',
  registration: '<rect x="82" y="48" width="228" height="240" rx="16"/><path d="M117 101h140M117 142h140M117 183h85M117 229h42M186 229h81"/><circle cx="270" cy="183" r="20"/>',
  checklist: '<rect x="79" y="47" width="236" height="246" rx="16"/><path d="m105 106 16 17 27-34M163 106h116M105 174l16 17 27-34M163 174h116M105 242l16 17 27-34M163 242h116"/>',
  compass: '<circle cx="196" cy="167" r="127"/><circle cx="196" cy="167" r="11"/><path d="m245 116-30 69-68 30 30-68zM196 41v22M196 270v22M70 167h22M300 167h22"/>',
  location: '<path d="M196 295c-41-64-105-121-105-180 0-59 47-103 105-103s105 44 105 103c0 59-64 116-105 180z"/><circle cx="196" cy="114" r="39"/>',
  drama: '<path d="M69 75c47 19 88 17 132 0v132c-20 65-116 65-132 0zM211 74c37 13 75 13 112 0v131c-14 58-75 73-112 47"/><path d="M105 128h15M155 128h15M108 190c13 13 42 13 54 0M241 126h14M284 126h14M241 193c12-12 37-12 50 0"/>',
  assembly: '<path d="M61 271h271M110 270v-93h172v93M91 175h210M194 175V88M156 88h77M127 126h136"/><circle cx="93" cy="83" r="19"/><circle cx="293" cy="83" r="19"/>',
  academic: '<path d="m37 128 159-73 159 73-159 72zM105 166v70c50 36 131 36 182 0v-70M355 128v116M340 251h30"/>'
};

try {
  entries.forEach((entry, index) => {
    const palette = palettes[(index + entry.slug.length) % palettes.length];
    const [dark, mid, gold, pale] = palette;
    const headlineLines = wrap(entry.headline, entry.headline.length > 22 ? 17 : 24);
    const fontSize = headlineLines.length > 2 ? 63 : 72;
    const startY = 273 - (headlineLines.length - 1) * 32;
    const headlines = headlineLines.map((line, row) => `<text x="504" y="${startY + row * 79}" fill="#fffaf1" font-family="Arial" font-size="${fontSize}" font-weight="bold">${escapeXml(line)}</text>`).join('');
    const motif = paths[entry.motif];
    if (!motif) throw new Error(`Unknown motif: ${entry.motif}`);
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="1280" height="720" viewBox="0 0 1280 720">
      <rect width="1280" height="720" fill="${dark}"/>
      <rect x="0" y="0" width="463" height="720" fill="${mid}" opacity=".44"/>
      <circle cx="1180" cy="-20" r="440" fill="none" stroke="${pale}" stroke-opacity=".13" stroke-width="2"/>
      <circle cx="1180" cy="-20" r="330" fill="none" stroke="${pale}" stroke-opacity=".1" stroke-width="2"/>
      <path d="M0 616C240 556 388 684 650 610S1084 544 1280 616" fill="none" stroke="${pale}" stroke-opacity=".12" stroke-width="2"/>
      <rect x="38" y="38" width="1204" height="644" rx="28" fill="none" stroke="${pale}" stroke-opacity=".22" stroke-width="2"/>
      <circle cx="246" cy="320" r="208" fill="${mid}" stroke="${pale}" stroke-opacity=".32" stroke-width="2"/>
      <circle cx="246" cy="320" r="185" fill="none" stroke="${gold}" stroke-opacity=".34" stroke-width="2" stroke-dasharray="5 15"/>
      <rect x="504" y="145" width="62" height="5" rx="2" fill="${gold}"/>
      <text x="504" y="192" fill="${gold}" font-family="Arial" font-size="19" font-weight="bold" letter-spacing="1">GSF NEWS · EDITORIAL COVER</text>
      ${headlines}
      <rect x="504" y="515" width="674" height="1" fill="${pale}" opacity=".45"/>
      <text x="504" y="561" fill="${pale}" font-family="Arial" font-size="24">${escapeXml(entry.kicker)}</text>
      <text x="506" y="630" fill="${pale}" font-family="Arial" font-size="17" letter-spacing="2">GOFAMINT STUDENTS’ FELLOWSHIP</text>
    </svg>`;
    const svgPath = join(scratch, `${index}.svg`);
    writeFileSync(svgPath, svg);
    execFileSync('magick', ['-font', font, '-background', 'none', svgPath, '-fill', 'none', '-stroke', gold, '-strokewidth', '10', '-draw', drawMotif(motif), '-strip', '-depth', '8', `PNG24:${join(destination, `${entry.slug}.png`)}`]);
  });
  process.stdout.write(`Generated ${entries.length} blog covers.\n`);
} finally {
  rmSync(scratch, { recursive: true, force: true });
}
