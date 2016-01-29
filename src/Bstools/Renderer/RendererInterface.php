<?php

namespace Bstools\Renderer;

interface RendererInterface
{
    /**
     * @param array $data
     *
     * @return string
     */
    public function render(array $data);
}
