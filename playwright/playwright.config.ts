import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  use: {
    baseURL: 'http://localhost:80',
    trace: 'on-first-retry',
    // headless: false,
    actionTimeout: 10000,    // アクションのタイムアウト（デフォルト: 5000ms）
    navigationTimeout: 10000, // ナビゲーションのタイムアウト（デフォルト: 5000ms）
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
}); 