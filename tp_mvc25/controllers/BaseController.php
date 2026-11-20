<?php
require_once __DIR__ . '/../views/Template.php';

/**
 * Fungsi umum yang dipakai seluruh controller:
 * membaca input, redirect, flash message, dan render halaman.
 */
abstract class BaseController
{
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function post(string $key): string
    {
        return trim((string) ($_POST[$key] ?? ''));
    }

    protected function redirect(string $page, string $action = 'index'): void
    {
        $url = 'index.php?page=' . $page . ($action !== 'index' ? '&action=' . $action : '');
        header('Location: ' . $url);
        exit;
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function render(string $title, string $active, string $content): void
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        Template::page($title, $active, $content, $flash);
    }

    /** Mengubah teks kosong menjadi NULL, selain itu menjadi integer. */
    protected function nullableInt(string $value): ?int
    {
        return $value === '' ? null : (int) $value;
    }
}
