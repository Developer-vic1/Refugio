import { cpSync, readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
function flattenCss(file, ancestors = []) {
  const source = resolve(file);
  if (ancestors.includes(source)) throw new Error(`Import CSS circular: ${source}`);
  return readFileSync(source, 'utf8').replace(/@import\s+['"](\.\.?\/[^'"]+)['"];?/g,
    (_, relative) => flattenCss(resolve(dirname(source), relative), [...ancestors, source]));
}
mkdirSync(resolve('public/assets/css'), { recursive: true });
writeFileSync(resolve('public/assets/css/app.css'), flattenCss('resources/css/app.css').trimEnd() + '\n');
cpSync(resolve('resources/js'), resolve('public/assets/js'), { recursive: true });
process.stdout.write('CSS modular compilado; módulos JavaScript sincronizados.\n');
