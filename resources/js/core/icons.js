const paths = {
  'chevron-down': 'm6 9 6 6 6-6',
  'chevron-left': 'm15 6-6 6 6 6',
  'chevron-right': 'm9 6 6 6-6 6',
  close: 'm6 6 12 12M6 18 18 6',
  calendar: 'M7 3v4m10-4v4M3 11h18M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z',
};
export function svgIcon(name) {
  const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  for (const [key,value] of Object.entries({class:'icon',viewBox:'0 0 24 24',fill:'none',stroke:'currentColor','stroke-width':'1.8','stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true',focusable:'false'})) svg.setAttribute(key,value);
  if (name === 'paw') {
    for (const [tag,attributes] of [['ellipse',{cx:8,cy:6,rx:2,ry:3}],['ellipse',{cx:16,cy:6,rx:2,ry:3}],['ellipse',{cx:4,cy:11,rx:2,ry:2.5}],['ellipse',{cx:20,cy:11,rx:2,ry:2.5}],['path',{d:'M6 19c0-3 3-7 6-7s6 4 6 7c0 4-4 1-6 1s-6 3-6-1Z'}]]) {
      const shape=document.createElementNS(svg.namespaceURI,tag);
      for (const [key,value] of Object.entries(attributes)) shape.setAttribute(key,String(value));
      svg.append(shape);
    }
    return svg;
  }
  const path = document.createElementNS(svg.namespaceURI, 'path');
  path.setAttribute('d', paths[name] || paths['chevron-down']);
  svg.append(path);
  return svg;
}
