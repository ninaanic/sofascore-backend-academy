<?php

declare(strict_types=1);

namespace App\Tools\Templating;

use App\Tools\Templating\Exception\FileNotFoundException;

final class Templating
{
    public function __construct(
        private string $basePath,
    ) {
    }

    public function render(string $template, array $variables = []): string
    {
        $templatesPath = $this->basePath.\DIRECTORY_SEPARATOR.$template;

        if (!file_exists($templatesPath)) {
            throw new FileNotFoundException($templatesPath);
        }

        ob_start();

        extract($variables);

        require $templatesPath;

        return ob_get_clean();
    }
}
