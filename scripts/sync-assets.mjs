import { copyFileSync, mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
for (const [source, target] of [
  ['resources/css/app.css', 'public/assets/css/app.css'],
  ['resources/js/app.js', 'public/assets/js/app.js']
]) {
  const out = resolve(target);
  mkdirSync(dirname(out), { recursive: true });
  copyFileSync(resolve(source), out);
  process.stdout.write(`Sincronizado: ${source} → ${target}\n`);
}
