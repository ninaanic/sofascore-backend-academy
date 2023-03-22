<?php

declare(strict_types=1);

namespace SimpleFW\Templating;

final readonly class Templating
{
    public function __construct(
        private string $basePath,
    ) {
    }

    public function render(string $template, array $variables = []): string
    {
        $templatesPath = $this->basePath.\DIRECTORY_SEPARATOR.$template;

        // @TODO: Zadatak 2

        if (!file_exists($templatesPath)) {
            throw new Exception\FileNotFoundException($templatesPath);
        }

        extract($variables);

        ob_start();
        include($templatesPath);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
