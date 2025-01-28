import { test, expect } from '@playwright/test';

test.describe('認証フロー', () => {
  const testUser = {
    name: 'テストユーザー',
    displayName: 'Test User',
    email: `test${Date.now()}@example.com`, // ユニークなメールアドレス
    password: 'testpass123'
  };

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

  test('アカウントを作成してログインできる', async ({ page }) => {
    // サインアップ
    await page.goto('/auth?mode=signup');
    await page.getByLabel('名前').fill(testUser.name);
    await page.getByLabel('表示名').fill(testUser.displayName);
    await page.getByLabel('メールアドレス').fill(testUser.email);
    await page.getByLabel('パスワード').fill(testUser.password);
    
    await page.getByRole('button', { name: 'サインアップ' }).click();

    // サインアップ後、ホームページにリダイレクトされることを確認
    await expect(page).toHaveURL('/');
    
    // ログアウト
    await page.getByRole('link', { name: 'サインアウト' }).click();

    // ログアウト後、ログインページにリダイレクトされることを確認
    await expect(page).toHaveURL('/auth/');
    
    // サインイン
    await page.goto('/auth');
    await page.getByLabel('メールアドレス').fill(testUser.email);
    await page.getByLabel('パスワード').fill(testUser.password);
    
    await page.getByRole('button', { name: 'サインイン' }).click();

    // サインイン後、ホームページにリダイレクトされることを確認
    await expect(page).toHaveURL('/');
    
    // ヘッダーにユーザー名が表示されていることを確認
    await expect(
      page.getByText(`サインイン中: ${testUser.displayName}`)
    ).toBeVisible();

    // ログアウト
    await page.getByRole('link', { name: 'サインアウト' }).click();

    // ログアウト後、ログインページにリダイレクトされることを確認
    await expect(page).toHaveURL('/auth/');
  });

  test('誤ったパスワードでログインできない', async ({ page }) => {
    await page.goto('/auth');
    await page.getByLabel('メールアドレス').fill(testUser.email);
    await page.getByLabel('パスワード').fill('wrongpassword');
    
    await page.getByRole('button', { name: 'サインイン' }).click();

    // エラーメッセージが表示されることを確認
    await expect(page.getByText('エラー:')).toBeVisible();
    // 認証ページに留まっていることを確認
    await expect(page).toHaveURL('/auth/');
  });
}); 