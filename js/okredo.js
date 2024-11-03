import puppeteer from 'puppeteer';
import fs from 'fs';

(async () => {
    let companyNames = fs.readFileSync(process.cwd() + '/../input/companies.csv', 'utf8').split(',');
    const fileName = `okredo-${Date.now()}.csv`;
    console.log(`Output file: ${fileName}`);
    fs.writeFileSync(process.cwd() + `/../output/${fileName}`, `Name,Code,Director,Phone number,Created at\n`);
    const browser = await puppeteer.launch();

    for (const el of companyNames) {
        try {
            const page = await browser.newPage();
            const url = `https://okredo.com/lt-lt/ieskoti?searchTerm=${encodeURI(el)}&pageNumber=1`;
            await page.goto(url);
            await page.waitForSelector('text/Veikiantis');
            await page.waitForSelector('text/Veikiantis');
            const companyNameSelector = await page.waitForSelector('.title-wrapper');
            const companyCodeSelector = await page.waitForSelector('text/Įmonės kodas');
            const createdAtSelector = await page.waitForSelector('text/Įmonės registracijos data');
            const phoneSelector = await page.waitForSelector('a.map-link-phone-line');
            const directorSelector = await page.waitForSelector('.map-link-manager');
            const companyCode = await companyCodeSelector?.evaluate(el => el.parentElement.querySelector('span').textContent);
            const createdAt = await createdAtSelector?.evaluate(el => el.parentElement.querySelector('span').textContent);
            const phone = await phoneSelector?.evaluate(el => el.textContent);
            const director = await directorSelector?.evaluate(el => el.textContent);
            const companyName = await companyNameSelector?.evaluate(el => el.textContent);
            fs.writeFileSync(process.cwd() + `/../output/${fileName}`, `"${companyName}",${companyCode},${director},${phone},${createdAt}\n`, { flag: 'a' });
        } catch (e) {
            console.error(`${el} Failed`);
        }
    }

    await browser.close();
})();
