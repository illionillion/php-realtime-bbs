import { test, expect } from '@playwright/test';

test.describe('スレッド機能', () => {
  const testUser = {
    name: 'スレッドテストユーザー',
    displayName: 'Thread Test User',
    email: `thread_test${Date.now()}@example.com`,
    password: 'testpass123'
  };

  test.beforeEach(async ({ page }) => {
    // テストユーザーでサインアップ
    await page.goto('/auth?mode=signup');
    await page.getByLabel('名前').fill(testUser.name);
    await page.getByLabel('表示名').fill(testUser.displayName);
    await page.getByLabel('メールアドレス').fill(testUser.email);
    await page.getByLabel('パスワード').fill(testUser.password);
    await page.getByRole('button', { name: 'サインアップ' }).click();
    
    // リダイレクトとページ読み込みを待機
    await page.waitForURL('/');
    await page.waitForLoadState('networkidle');
    
    await expect(page).toHaveURL('/');
  });

  test('新しいスレッドを作成できる', async ({ page }) => {
    const threadTitle = `テストスレッド${Date.now()}`;

    // スレッドのタイトルを入力
    await page.getByLabel('スレッドのタイトル').fill(threadTitle);

    // 作成ボタンをクリック
    await page.getByRole('button', { name: '作成する' }).click();

    // スレッド一覧に新しいスレッドが表示されることを確認
    const threadElement = page.getByRole('listitem').filter({ hasText: threadTitle });
    await expect(threadElement).toBeVisible();

    // 作成者名と作成日時が表示されることを確認
    await expect(threadElement.getByText(`作成者: ${testUser.displayName}`)).toBeVisible();
    // 作成日時のフォーマットは "YYYY/MM/DD HH:mm" であることを確認
    await expect(threadElement.getByText(/\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}/)).toBeVisible();
  });

  test('スレッド作成時にエラーが表示される', async ({ page }) => {
    // タイトルを入力せずに作成ボタンをクリック
    await page.getByRole('button', { name: '作成する' }).click();

    // required属性によってブラウザのバリデーションが働くことを確認
    await expect(page.getByLabel('スレッドのタイトル')).toHaveAttribute('required', '');
  });

  test.afterEach(async ({ page }) => {
    // ログアウト前にページが安定するのを待機
    await page.waitForLoadState('networkidle');
    
    await page.getByRole('link', { name: 'サインアウト' }).click();
    
    // ログアウト後のリダイレクトを待機
    await page.waitForURL('/auth/');
    await expect(page).toHaveURL('/auth/');
  });
}); 