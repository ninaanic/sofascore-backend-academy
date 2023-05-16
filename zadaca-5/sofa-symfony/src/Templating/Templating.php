<?php

declare(strict_types=1);

namespace App\Templating;

use App\Templating\Exception\FileNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class Templating
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/templates')]
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
