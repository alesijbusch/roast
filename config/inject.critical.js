const options = require('../options')
const glob = require('glob');
const fs = require('fs');
const path = require('path');

const pagesNames = options.pages === 'all' ? '*' : options.pages;
const pagesGlob = glob.sync(`html/${pagesNames}.html`);

pagesGlob.forEach(page => {
  let styles = [];
  let html = fs.readFileSync(page, 'utf8')
  const linkRegExp = /<link .*components-template\/.*\.css.*>/g
  const links = html.match(linkRegExp);
  links.forEach(link => {
    const html = fs.readFileSync(page, 'utf8')
    const linkPath = link.match(/components-template\/.*\.css/g)
    const pathToStyle = path.resolve(__dirname, `../html/${linkPath}`);
    styles.push( fs.readFileSync(pathToStyle, 'utf-8'));
    const newHTML =  html.replace(link, '');
    fs.writeFileSync(page, newHTML)
  })
  html = fs.readFileSync(page, 'utf8');
  const replaceItem = html.match(/<style data="component"><\/style>/);
  const newHtml = html.replace(replaceItem, styles.map( style => `<style>${style}</style>`).join('\n'))
  fs.writeFileSync(page, newHtml);
  styles = [];
})