<?php

namespace Filapress\RichEditor\Support;

use Illuminate\Support\HtmlString;

class HelpView
{

    protected string $heading;

    protected ?string $html = null;

    protected ?string $text = null;

    protected ?array $view = null;

    public function __construct(string $heading) {

        $this->heading = $heading;
    }

    public static function make(string $heading): static
    {
        return new static($heading);
    }

    public function text(?string $text): static
    {
        $this->text = $text;
        return $this;
    }

    public function html(?string $html): static
    {
        $this->html = $html;
        return $this;
    }

    public function view (string $viewName, array $viewData = []): static
    {
        $this->view = [
            'view' => $viewName,
            'data' => $viewData,
        ];

        return $this;
    }


    public function render() {
        if ($this->view) {

        }

        return view('filapress-rich-editor::help.item', [
            'heading' => $this->heading,
            'body' => $this->html ? new HtmlString($this->html) : $this->text,
        ]);
    }
}
