import { test, expect } from '@playwright/test';

test.describe('認証フロー', () => {
  test('サインインとサインアップの切り替えができる', async ({ page }) => {
    // 認証ページに移動
    await page.goto('/auth');
    
    // デフォルトでサインインタブがアクティブであることを確認
    await expect(page.getByRole('link', { name: 'サインイン' }))
      .toHaveClass(/active/);
    
    // サインアップタブをクリック
    await page.getByRole('link', { name: 'サインアップ' }).click();
    
    // URLにmode=signupが含まれていることを確認
    await expect(page).toHaveURL(/mode=signup/);
    
    // 名前フィールドが表示されていることを確認
    await expect(page.getByLabel('名前')).toBeVisible();
    
    // サインインタブに戻る
    await page.getByRole('link', { name: 'サインイン' }).click();
    
    // 名前フィールドが非表示になっていることを確認
    await expect(page.getByLabel('名前')).not.toBeVisible();
  });
}); 