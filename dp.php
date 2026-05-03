<?php
require_once __DIR__ . '/functions.php';

session_start();

$action = $_REQUEST['action'] ?? '';
$message = '';

if ($action === 'logout') {
    logout();
    header('Location: dp.php');
    exit;
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        header('Location: dp.php');
        exit;
    }
    $message = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
}

if (!isLoggedIn() && $action !== 'login') {
    header('Location: dp.php?action=login');
    exit;
}

if (isLoggedIn()) {
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        if ($title !== '') {
            addItem($title, $description);
            $message = 'تمت إضافة العنصر بنجاح.';
        } else {
            $message = 'يجب إضافة عنوان.';
        }
    }

    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? '';
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        if ($id !== '' && $title !== '') {
            updateItem($id, $title, $description);
            $message = 'تم تحديث العنصر بنجاح.';
        } else {
            $message = 'يجب اختيار عنصر صحيح وكتابة عنوان.';
        }
    }

    if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? '';
        if ($id !== '') {
            deleteItem($id);
            $message = 'تم حذف العنصر بنجاح.';
        }
    }
}

$items = isLoggedIn() ? loadItems() : [];
$editItem = null;
if ($action === 'edit' && isLoggedIn()) {
    $id = $_GET['id'] ?? '';
    $editItem = getItemById($items, $id);
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تطبيق CRUD بسيط</title>
    <style>
        body { font-family: Arial, sans-serif; direction: rtl; text-align: right; margin: 0; padding: 0; background: #f4f4f4; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        h1 { margin-top: 0; }
        form { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type=text], textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { min-height: 100px; resize: vertical; }
        button { padding: 10px 18px; border: none; border-radius: 4px; background: #0078d4; color: #fff; cursor: pointer; }
        button:hover { background: #005a9e; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; }
        th { background: #f8f8f8; }
        .actions button { margin-left: 8px; }
        .message { padding: 12px; background: #e7f5e6; color: #1b6b28; border: 1px solid #cde5cd; border-radius: 4px; margin-bottom: 20px; }
        .login-card { max-width: 420px; margin: 80px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.08); }
        .login-card h2 { margin-top: 0; }
        .logout { float: left; }
    </style>
</head>
<body>
<div class="container">
    <?php if (!isLoggedIn()): ?>
        <div class="login-card">
            <h2>تسجيل الدخول</h2>
            <?php if ($message): ?><div class="message"><?= $message ?></div><?php endif; ?>
            <form method="post" action="?action=login">
                <label>اسم المستخدم</label>
                <input type="text" name="username" required>
                <label>كلمة المرور</label>
                <input type="text" name="password" required>
                <button type="submit">دخول</button>
            </form>
            <p>المستخدم الافتراضي: <strong>admin</strong>، كلمة المرور: <strong>password</strong></p>
        </div>
    <?php else: ?>
        <div>
            <a class="logout" href="?action=logout">تسجيل خروج</a>
            <h1>نظام CRUD بسيط</h1>
            <?php if ($message): ?><div class="message"><?= $message ?></div><?php endif; ?>
            <form method="post" action="?action=<?= $editItem ? 'update' : 'create' ?>">
                <?php if ($editItem): ?>
                    <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
                <?php endif; ?>
                <label>العنوان</label>
                <input type="text" name="title" value="<?= $editItem ? sanitize($editItem['title']) : '' ?>" required>
                <label>الوصف</label>
                <textarea name="description"><?= $editItem ? sanitize($editItem['description']) : '' ?></textarea>
                <button type="submit"><?= $editItem ? 'تحديث' : 'إضافة' ?></button>
                <?php if ($editItem): ?>
                    <a href="dp.php">إلغاء</a>
                <?php endif; ?>
            </form>

            <h2>قائمة العناصر</h2>
            <?php if (empty($items)): ?>
                <p>لا توجد عناصر حتى الآن.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>العنوان</th>
                            <th>الوصف</th>
                            <th>تم الإنشاء</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= sanitize($item['title']) ?></td>
                                <td><?= sanitize($item['description']) ?></td>
                                <td><?= sanitize($item['created_at'] ?? '') ?></td>
                                <td class="actions">
                                    <a href="?action=edit&id=<?= $item['id'] ?>">تعديل</a>
                                    <form method="post" action="?action=delete" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button type="submit" onclick="return confirm('هل أنت متأكد من الحذف؟');">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
