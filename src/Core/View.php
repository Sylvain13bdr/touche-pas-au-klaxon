<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Moteur de rendu de vues très simple basé sur des templates PHP.
 *
 * Une vue est rendue puis injectée dans un layout via la variable
 * $content. Les données sont extraites en variables locales du template.
 */
final class View
{
    private string $viewsPath;

    public function __construct(?string $viewsPath = null)
    {
        $this->viewsPath = $viewsPath ?? dirname(__DIR__) . '/Views';
    }

    /**
     * Rend un template dans un layout et retourne le HTML complet.
     *
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = [], string $layout = 'layouts/main'): string
    {
        $content = $this->renderPartial($template, $data);

        return $this->renderPartial($layout, array_merge($data, ['content' => $content]));
    }

    /**
     * Rend un template seul (sans layout) et retourne le HTML.
     *
     * @param array<string, mixed> $data
     *
     * @throws RuntimeException si le template est introuvable.
     */
    public function renderPartial(string $template, array $data = []): string
    {
        $file = $this->viewsPath . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException("Vue introuvable : {$template}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $file;

        return (string) ob_get_clean();
    }

    /**
     * Échappe une valeur pour un affichage HTML sûr (anti-XSS).
     */
    public function e(int|float|string|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
