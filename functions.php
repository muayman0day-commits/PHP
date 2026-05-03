<?php

function loadConfig(): array
{
    return require __DIR__ . '/config.php';
}

function getDataFile(): string
{
    $config = loadConfig();
    return $config['data_file'];
}

function loadItems(): array
{
    $file = getDataFile();
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    $json = file_get_contents($file);
    $items = json_decode($json, true);
    return is_array($items) ? $items : [];
}

function saveItems(array $items): void
{
    file_put_contents(getDataFile(), json_encode(array_values($items), JSON_PRETTY_PRINT));
}

function getItemById(array $items, string $id): ?array
{
    foreach ($items as $item) {
        if ((string)$item['id'] === (string)$id) {
            return $item;
        }
    }
    return null;
}

function addItem(string $title, string $description): void
{
    $items = loadItems();
    $items[] = [
        'id' => uniqid('', true),
        'title' => $title,
        'description' => $description,
        'created_at' => date('Y-m-d H:i:s'),
    ];
    saveItems($items);
}

function updateItem(string $id, string $title, string $description): void
{
    $items = loadItems();
    foreach ($items as &$item) {
        if ((string)$item['id'] === (string)$id) {
            $item['title'] = $title;
            $item['description'] = $description;
            $item['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    saveItems($items);
}

function deleteItem(string $id): void
{
    $items = loadItems();
    $items = array_filter($items, fn($item) => (string)$item['id'] !== (string)$id);
    saveItems(array_values($items));
}

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function isLoggedIn(): bool
{
    session_start();
    return !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ?action=login');
        exit;
    }
}

function authenticate(string $username, string $password): bool
{
    $config = loadConfig();
    return $username === $config['admin_user'] && $password === $config['admin_pass'];
}

function login(string $username, string $password): bool
{
    if (authenticate($username, $password)) {
        $_SESSION['logged_in'] = true;
        return true;
    }
    return false;
}

function logout(): void
{
    session_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}
