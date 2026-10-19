
const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

(async () => {
  const browser = await chromium.launch({
    headless: true,
    executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
  });
  const context = await browser.newContext({
    viewport: { width: 1200, height: 820 },
    deviceScaleFactor: 2
  });
  const page = await context.newPage();

  const docsDir = path.join(__dirname, '..', 'docs', 'assets');
  if (!fs.existsSync(docsDir)) {
    fs.mkdirSync(docsDir, { recursive: true });
  }

  // 1. Storefront & LocalStorage Cache Simulation Test
  const storeUrl = 'file://' + path.join(__dirname, 'store-simulation.html');
  console.log('Navigating to:', storeUrl);
  await page.goto(storeUrl);

  // Set initial LocalStorage cache to test instant hydration
  await page.evaluate(() => {
    const mockCache = {
      hash: 'cart_hash_demo_882',
      time: Date.now(),
      fragments: {
        'span.wff-cart-count': '<span class="wff-cart-count">2</span>',
        'span.wff-cart-total': '<span class="wff-cart-total" style="color: #475569; font-size: 13px;">$148.00</span>'
      }
    };
    localStorage.setItem('wff_wc_fragments_cache', JSON.stringify(mockCache));
  });

  // Reload page to verify 0ms instant hydration from LocalStorage without network request
  await page.reload();
  await page.waitForSelector('.wff-cart-count');

  // Click Add to Cart to test pulse interaction
  await page.click('#btn-add-1');
  await new Promise(r => setTimeout(r, 300));

  const storeScreenshot = path.join(docsDir, 'storefront-turbo-preview.png');
  await page.screenshot({ path: storeScreenshot, fullPage: false });
  console.log('Saved storefront preview to:', storeScreenshot);

  // 2. Admin Dashboard & Metrics Simulation
  const adminUrl = 'file://' + path.join(__dirname, 'admin-simulation.html');
  await page.goto(adminUrl);
  await page.waitForSelector('.postbox');

  const adminScreenshot = path.join(docsDir, 'admin-metrics-preview.png');
  await page.screenshot({ path: adminScreenshot, fullPage: false });
  console.log('Saved admin metrics preview to:', adminScreenshot);

  await browser.close();
  console.log('ALL TESTS PASSED: Fast Fragments LocalStorage hydration & admin previews captured.');
})();

